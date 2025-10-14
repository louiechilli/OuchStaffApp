<?php

declare(strict_types=1);

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event as GEvent;
use Google\Service\Calendar\EventDateTime;
use Google\Service\Calendar\EventAttendee;
use Google\Service\Calendar\EventReminders;
use Google\Service\Calendar\ConferenceData;
use Google\Service\Calendar\CreateConferenceRequest;
use Google\Service\Calendar\ConferenceSolutionKey;

/**
 * Google Calendar integration service with full logging.
 */
class GoogleCalendarService
{
    private Calendar $calendar;
    private \DateTimeZone $defaultTz;
    private ?string $impersonate = null;
    private string $credentialsPath;

    public function __construct(string $credentialsPath, ?string $impersonate = null)
    {
        \Log::debug('GoogleCalendarService::__construct', [
            'credentialsPath' => $credentialsPath,
            'impersonate'     => $impersonate,
        ]);

        $this->defaultTz = new \DateTimeZone(\env('BOOKING_TZ', 'Europe/London'));
        $this->impersonate = $impersonate;
        $this->credentialsPath = $credentialsPath;
        $this->calendar = $this->buildCalendarClient($impersonate);
    }

    public function listEvents(string $calendarId = 'primary', int $maxResults = 10): array
    {
        \Log::info('GoogleCalendarService::listEvents called', [
            'calendarId' => $calendarId,
            'maxResults' => $maxResults,
        ]);

        try {
            $events = $this->retryOnInvalidGrant(fn () => $this->calendar->events->listEvents($calendarId, [
                'maxResults'   => $maxResults,
                'orderBy'      => 'startTime',
                'singleEvents' => true,
                'timeZone'     => $this->defaultTz->getName(),
            ]));
            \Log::info('GoogleCalendarService::listEvents success', ['count' => \count($events->getItems())]);
            return $events->getItems();
        } catch (\Throwable $e) {
            \Log::error('GoogleCalendarService::listEvents failed', ['error' => $e->getMessage()]);
            throw new \RuntimeException('Failed to list events.');
        }
    }

    public function getAvailableDays(string $calendarId, int $durationMinutes, string $timeMin, string $timeMax): array
    {
        \Log::info('GoogleCalendarService::getAvailableDays called', [
            'calendarId'      => $calendarId,
            'durationMinutes' => $durationMinutes,
            'timeMin'         => $timeMin,
            'timeMax'         => $timeMax,
        ]);

        $tz = $this->defaultTz;
        $startPeriod = $this->immutable($timeMin, $tz);
        $endPeriod   = $this->immutable($timeMax, $tz);

        try {
            $events = $this->retryOnInvalidGrant(fn () => $this->calendar->events->listEvents($calendarId, [
                'orderBy'      => 'startTime',
                'singleEvents' => true,
                'timeMin'      => $startPeriod->format('Y-m-d\TH:i:sP'),
                'timeMax'      => $endPeriod->format('Y-m-d\TH:i:sP'),
                'timeZone'     => $tz->getName(),
            ]))->getItems();
            \Log::debug('Fetched events for availability check', ['count' => \count($events)]);
        } catch (\Throwable $e) {
            \Log::error('GoogleCalendarService::getAvailableDays query failed', ['error' => $e->getMessage()]);
            throw new \RuntimeException('Failed to query Google Calendar.');
        }

        // Busy slots grouping
        $busyByDay = [];
        foreach ($events as $event) {
            $range = $this->extractEventRange($event, $tz);
            if (!$range) { continue; }
            [$startTs, $endTs] = $range;
            $startDate = (new \DateTime('@' . $startTs))->setTimezone($tz)->format('Y-m-d');
            $busyByDay[$startDate][] = ['start' => $startTs, 'end' => $endTs];
        }
        \Log::debug('Busy slots grouped by day', $busyByDay);

        $availableDays = [];
        for ($date = $startPeriod; $date <= $endPeriod; $date = $date->modify('+1 day')) {
            $dayStr = $date->format('Y-m-d');
            [$workStart, $workEnd] = $this->workingWindow($dayStr, $tz);

            $busy = $busyByDay[$dayStr] ?? [];
            \usort($busy, static fn ($a, $b) => $a['start'] <=> $b['start']);

            $cursor  = $workStart;
            $hasSlot = false;
            foreach ($busy as $slot) {
                if ($slot['start'] - $cursor >= $durationMinutes * 60) {
                    $hasSlot = true; break;
                }
                $cursor = max($cursor, $slot['end']);
            }
            if (!$hasSlot && $workEnd - $cursor >= $durationMinutes * 60) {
                $hasSlot = true;
            }

            if ($hasSlot) { 
                $availableDays[] = $dayStr; 
                \Log::debug("Day $dayStr has availability");
            } else {
                \Log::debug("Day $dayStr has no availability");
            }
        }

        \Log::info('GoogleCalendarService::getAvailableDays complete', [
            'availableDays' => $availableDays,
        ]);

        return $availableDays;
    }

    public function getAvailableTimeSlots(string $calendarId, int $durationMinutes, string $date): array
    {
        \Log::info('GoogleCalendarService::getAvailableTimeSlots called', [
            'calendarId'      => $calendarId,
            'durationMinutes' => $durationMinutes,
            'date'            => $date,
        ]);

        $tz = $this->defaultTz;
        $timeMinObj = $this->immutable($date . ' 00:00:00', $tz);
        $timeMaxObj = $this->immutable($date . ' 23:59:59', $tz);

        $timeMin = $timeMinObj->format('Y-m-d\TH:i:sP');
        $timeMax = $timeMaxObj->format('Y-m-d\TH:i:sP');

        try {
            $events = $this->retryOnInvalidGrant(fn () => $this->calendar->events->listEvents($calendarId, [
                'orderBy'      => 'startTime',
                'singleEvents' => true,
                'timeMin'      => $timeMin,
                'timeMax'      => $timeMax,
                'timeZone'     => $tz->getName(),
            ]))->getItems();
            \Log::debug('Fetched events for time slot check', ['count' => \count($events)]);
        } catch (\Throwable $e) {
            \Log::error('GoogleCalendarService::getAvailableTimeSlots query failed', ['error' => $e->getMessage()]);
            throw new \RuntimeException('Failed to query Google Calendar.');
        }

        $busy = [];
        foreach ($events as $event) {
            $range = $this->extractEventRange($event, $tz);
            if ($range) {
                [$startTs, $endTs] = $range;
                $busy[] = ['start' => $startTs, 'end' => $endTs];
            }
        }
        \usort($busy, static fn ($a, $b) => $a['start'] <=> $b['start']);
        \Log::debug('Busy slots', $busy);

        [$workStart, $workEnd] = $this->workingWindow($date, $tz);
        \Log::debug('Working window', ['start' => $workStart, 'end' => $workEnd]);

        $slots  = [];
        $cursor = $workStart;
        foreach ($busy as $slot) {
            while ($cursor + $durationMinutes * 60 <= $slot['start']) {
                if ($cursor + $durationMinutes * 60 <= $workEnd) {
                    $startObj = (new \DateTime('@' . $cursor))->setTimezone($tz);
                    $endObj   = (new \DateTime('@' . ($cursor + $durationMinutes * 60)))->setTimezone($tz);
                    $slots[]  = ['start' => $startObj->format('H:i'), 'end' => $endObj->format('H:i')];
                }
                $cursor += $durationMinutes * 60;
            }
            $cursor = max($cursor, $slot['end']);
        }

        while ($cursor + $durationMinutes * 60 <= $workEnd) {
            $startObj = (new \DateTime('@' . $cursor))->setTimezone($tz);
            $endObj   = (new \DateTime('@' . ($cursor + $durationMinutes * 60)))->setTimezone($tz);
            $slots[]  = ['start' => $startObj->format('H:i'), 'end' => $endObj->format('H:i')];
            $cursor  += $durationMinutes * 60;
        }

        \Log::info('GoogleCalendarService::getAvailableTimeSlots complete', ['slots' => $slots]);
        return $slots;
    }

    public function createEvent(
        string $calendarId,
        string $summary,
        string|\DateTimeInterface $start,
        string|\DateTimeInterface $end,
        ?string $description = null,
        ?string $timezone = null,
        array $attendeeEmails = [],
        bool $addMeetLink = false,
        ?int $popupReminderMins = null
    ): \Google\Service\Calendar\Event {
        \Log::info('GoogleCalendarService::createEvent called', [
            'calendarId'       => $calendarId,
            'summary'          => $summary,
            'description'      => $description,
            'timezone'         => $timezone,
            'attendees'        => $attendeeEmails,
            'addMeetLink'      => $addMeetLink,
            'popupReminderMins'=> $popupReminderMins,
            'impersonate'      => $this->impersonate,
        ]);

        $tzName = $timezone ?: $this->defaultTz->getName();

        // Build typed EventDateTime objects
        $startDT = new EventDateTime();
        $startDT->setDateTime($this->toRfc3339($start, $tzName));
        $startDT->setTimeZone($tzName);

        $endDT = new EventDateTime();
        $endDT->setDateTime($this->toRfc3339($end, $tzName));
        $endDT->setTimeZone($tzName);

        $event = new GEvent();
        $event->setSummary($summary);
        if ($description) { $event->setDescription($description); }
        $event->setStart($startDT);
        $event->setEnd($endDT);

        if (!empty($attendeeEmails)) {
            $attendees = [];
            foreach ($attendeeEmails as $email) {
                if (!$email) { continue; }
                $a = new EventAttendee();
                $a->setEmail($email);
                $attendees[] = $a;
            }
            $attendees = [];
            if ($attendees) {
                $event->setAttendees($attendees);
            }
        }

        if ($popupReminderMins !== null) {
            $reminders = new EventReminders();
            $reminders->setUseDefault(false);
            $reminders->setOverrides([['method' => 'popup', 'minutes' => $popupReminderMins]]);
            $event->setReminders($reminders);
        }

        $wantsMeet = $addMeetLink;
        if ($wantsMeet) {
            // Proper Meet payload
            $key = new ConferenceSolutionKey();
            $key->setType('hangoutsMeet');

            $req = new CreateConferenceRequest();
            $req->setRequestId(bin2hex(random_bytes(8)));
            $req->setConferenceSolutionKey($key);

            $conf = new ConferenceData();
            $conf->setCreateRequest($req);

            $event->setConferenceData($conf);
        }

        try {
            /** @var \Google\Service\Calendar\Event $created */
            $created = $this->retryOnInvalidGrant(fn () => $this->calendar->events->insert(
                $calendarId,
                $event,
                [
                    'conferenceDataVersion' => $wantsMeet ? 1 : 0,
                    'sendUpdates'           => 'none', // safer default
                ]
            ));
            \Log::info('GoogleCalendarService::createEvent success', [
                'id'       => $created->getId(),
                'htmlLink' => $created->getHtmlLink(),
                'hangoutLink' => $created->getHangoutLink(),
            ]);
            return $created;

        } catch (\Google\Service\Exception $ge) {
            $message = $ge->getMessage();
            \Log::warning('GoogleCalendarService::createEvent API error — first attempt', [
                'message' => $message,
                'code'    => $ge->getCode(),
                'errors'  => method_exists($ge, 'getErrors') ? $ge->getErrors() : null,
            ]);

            // Common org-policy case: Meet creation forbidden. Retry without Meet.
            $shouldRetryWithoutMeet = $wantsMeet && (
                str_contains($message, 'conference') ||
                str_contains($message, 'forbidden') ||
                str_contains($message, 'notAuthorized') ||
                str_contains($message, 'insufficientPermissions') ||
                str_contains($message, 'Invalid conference')
            );

            if ($shouldRetryWithoutMeet) {
                $event->setConferenceData(null);
                try {
                    /** @var \Google\Service\Calendar\Event $created */
                    $created = $this->retryOnInvalidGrant(fn () => $this->calendar->events->insert(
                        $calendarId,
                        $event,
                        [
                            'conferenceDataVersion' => 0,
                            'sendUpdates'           => 'none',
                        ]
                    ));
                    \Log::info('GoogleCalendarService::createEvent success — retried without Meet', [
                        'id'       => $created->getId(),
                        'htmlLink' => $created->getHtmlLink(),
                    ]);
                    return $created;
                } catch (\Throwable $e2) {
                    \Log::error('GoogleCalendarService::createEvent failed even without Meet', ['error' => $e2->getMessage()]);
                    throw new \RuntimeException('Failed to create event (even without Meet): ' . $e2->getMessage());
                }
            }

            // Common access issues: not impersonating a user who owns the calendar
            if (str_contains($message, 'Not Found') || str_contains($message, 'forbidden') || str_contains($message, 'notAuthorized')) {
                \Log::error('GoogleCalendarService::createEvent likely access error', [
                    'calendarId'  => $calendarId,
                    'impersonate' => $this->impersonate,
                    'hint'        => 'Ensure the service account has domain-wide delegation AND impersonates a user who has access to this calendar, or use a calendar owned by the service account and share it.',
                ]);
            }

            throw new \RuntimeException('Failed to create event: ' . $message);

        } catch (\Throwable $e) {
            \Log::error('GoogleCalendarService::createEvent failed (non-API)', ['error' => $e->getMessage()]);
            throw new \RuntimeException('Failed to create event.');
        }
    }

    // ----------------- Helpers with logging -----------------

    private function immutable(string $time, \DateTimeZone $tz): \DateTimeImmutable
    {
        \Log::debug('GoogleCalendarService::immutable', ['time' => $time, 'tz' => $tz->getName()]);
        return new \DateTimeImmutable($time, $tz);
    }

    private function workingWindow(string $localDateYmd, \DateTimeZone $tz): array
    {
        $startStr = \env('BOOKING_WORK_START', '09:00:00');
        $endStr   = \env('BOOKING_WORK_END',   '17:00:00');

        $workStart = (new \DateTime($localDateYmd . ' ' . $startStr, $tz))->getTimestamp();
        $workEnd   = (new \DateTime($localDateYmd . ' ' . $endStr,   $tz))->getTimestamp();

        \Log::debug('GoogleCalendarService::workingWindow', [
            'date'      => $localDateYmd,
            'workStart' => $workStart,
            'workEnd'   => $workEnd,
        ]);

        return [$workStart, $workEnd];
    }

    private function extractEventRange(object $event, \DateTimeZone $tz): ?array
    {
        $startRaw = $event->start->dateTime ?? $event->start->date ?? null;
        $endRaw   = $event->end->dateTime   ?? $event->end->date   ?? null;
        \Log::debug('GoogleCalendarService::extractEventRange raw', ['start' => $startRaw, 'end' => $endRaw]);

        if (!$startRaw || !$endRaw) { return null; }

        if (isset($event->start->date) || (\is_string($startRaw) && \strlen($startRaw) === 10)) {
            $start = new \DateTime($startRaw . ' 00:00:00', $tz);
            $end   = new \DateTime($endRaw   . ' 23:59:59', $tz);
        } else {
            $start = new \DateTime((string) $startRaw);
            $end   = new \DateTime((string) $endRaw);
            $start->setTimezone($tz);
            $end->setTimezone($tz);
        }

        $result = [$start->getTimestamp(), $end->getTimestamp()];
        \Log::debug('GoogleCalendarService::extractEventRange parsed', $result);
        return $result;
    }

    private function toRfc3339(string|\DateTimeInterface $value, string $tzName): string
    {
        $tz = new \DateTimeZone($tzName);
        if ($value instanceof \DateTimeInterface) {
            $dt = (new \DateTimeImmutable('@' . $value->getTimestamp()))->setTimezone($tz);
        } else {
            $dt = new \DateTimeImmutable($value, $tz);
        }
        $formatted = $dt->format('c');
        \Log::debug('GoogleCalendarService::toRfc3339', ['input' => $value, 'tz' => $tzName, 'output' => $formatted]);
        return $formatted;
    }

    private function buildCalendarClient(?string $subject): Calendar
    {
        \Log::debug('GoogleCalendarService::buildCalendarClient', ['subject' => $subject]);
        $client = new Client();
        $client->setAuthConfig($this->credentialsPath);
        // Full Calendar scope includes events; readonly is not used.
        $client->setScopes([Calendar::CALENDAR]);
        $client->setApplicationName(config('app.name', 'Booking System'));
        if ($subject) { $client->setSubject($subject); }
        return new Calendar($client);
    }

    private function retryOnInvalidGrant(callable $fn)
    {
        try {
            return $fn();
        } catch (\Throwable $e) {
            $msg = (string) $e->getMessage();
            \Log::warning('GoogleCalendarService::retryOnInvalidGrant caught error', ['error' => $msg]);
            if ($this->impersonate && str_contains($msg, 'invalid_grant')) {
                \Log::warning('Retrying without impersonation', ['subject' => $this->impersonate]);
                $this->calendar = $this->buildCalendarClient(null);
                $this->impersonate = null;
                return $fn();
            }
            throw $e;
        }
    }
}