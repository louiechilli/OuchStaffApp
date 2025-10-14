<?php

namespace Database\Seeders;

use App\Models\DocumentTemplate;
use App\Models\Service;
use Illuminate\Database\Seeder;

class DocumentTemplateSeeder extends Seeder
{
    public function run(): void
    {
        // Example: Tattoo Consent Form
        $tattooConsent = DocumentTemplate::create([
            'name' => 'Tattoo Consent Form',
            'slug' => 'tattoo-consent-form',
            'description' => 'Standard consent form required before any tattoo service',
            'is_active' => true,
            'requires_signature' => true,
            'display_order' => 1,
            'content' => $this->getTattooConsentContent(),
        ]);

        // Example: Piercing Consent Form
        $piercingConsent = DocumentTemplate::create([
            'name' => 'Piercing Consent Form',
            'slug' => 'piercing-consent-form',
            'description' => 'Standard consent form required before any piercing service',
            'is_active' => true,
            'requires_signature' => true,
            'display_order' => 2,
            'content' => $this->getPiercingConsentContent(),
        ]);

        // Example: Health & Safety Waiver
        $healthWaiver = DocumentTemplate::create([
            'name' => 'Health & Safety Waiver',
            'slug' => 'health-safety-waiver',
            'description' => 'Health and safety acknowledgment and waiver',
            'is_active' => true,
            'requires_signature' => true,
            'display_order' => 3,
            'content' => $this->getHealthWaiverContent(),
        ]);

        // Attach documents to services (example)
        // You'll need to adjust this based on your actual service IDs
        $tattooServices = Service::where('name', 'like', '%tattoo%')->get();
        foreach ($tattooServices as $service) {
            $service->documentTemplates()->attach($tattooConsent->id, [
                'is_required' => true,
                'display_order' => 1,
            ]);
            $service->documentTemplates()->attach($healthWaiver->id, [
                'is_required' => true,
                'display_order' => 2,
            ]);
        }

        $piercingServices = Service::where('name', 'like', '%piercing%')->get();
        foreach ($piercingServices as $service) {
            $service->documentTemplates()->attach($piercingConsent->id, [
                'is_required' => true,
                'display_order' => 1,
            ]);
            $service->documentTemplates()->attach($healthWaiver->id, [
                'is_required' => true,
                'display_order' => 2,
            ]);
        }
    }

    private function getTattooConsentContent(): string
    {
        return <<<HTML
<div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;">
    <h1 style="text-align: center; color: #333;">Tattoo Consent Form</h1>
    <p style="text-align: center; color: #666;">Ouch Tattoo Studio</p>
    
    <div style="margin: 30px 0;">
        <p><strong>Client Name:</strong> {{client_name}}</p>
        <p><strong>Date:</strong> {{current_date}}</p>
        <p><strong>Artist:</strong> {{artist_name}}</p>
        <p><strong>Service:</strong> {{service_name}}</p>
    </div>
    
    <h2 style="color: #333; margin-top: 30px;">Consent and Acknowledgment</h2>
    
    <div style="line-height: 1.8; color: #333;">
        <p>I, <strong>{{client_name}}</strong>, hereby consent to receive tattoo services from <strong>{{artist_name}}</strong> at Ouch Tattoo Studio.</p>
        
        <h3>I acknowledge and agree to the following:</h3>
        <ul>
            <li>I am at least 18 years of age and have provided valid identification.</li>
            <li>I am not under the influence of alcohol or drugs.</li>
            <li>I do not have any medical conditions that would prevent me from getting a tattoo.</li>
            <li>I understand that tattoos are permanent and removal is difficult and expensive.</li>
            <li>I have been given the opportunity to ask questions about the procedure and aftercare.</li>
            <li>I understand the risks including infection, scarring, and allergic reactions.</li>
            <li>I will follow all aftercare instructions provided by the artist.</li>
            <li>I release Ouch Tattoo Studio and its artists from any liability arising from this tattoo.</li>
        </ul>
        
        <h3 style="margin-top: 20px;">Aftercare Responsibilities</h3>
        <p>I understand that proper aftercare is my responsibility and failure to follow instructions may result in infection or poor healing.</p>
        
        <h3 style="margin-top: 20px;">Payment Agreement</h3>
        <p>I agree to pay the full amount for services rendered and understand that deposits are non-refundable.</p>
    </div>
    
    <div style="margin-top: 50px; padding: 20px; background: #f5f5f5; border-radius: 5px;">
        <p style="margin: 0;"><strong>Booking ID:</strong> {{booking_id}}</p>
        <p style="margin: 10px 0 0 0;"><strong>Appointment Date:</strong> {{booking_date}} at {{booking_time}}</p>
    </div>
</div>
HTML;
    }

    private function getPiercingConsentContent(): string
    {
        return <<<HTML
<div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;">
    <h1 style="text-align: center; color: #333;">Piercing Consent Form</h1>
    <p style="text-align: center; color: #666;">Ouch Tattoo Studio</p>
    
    <div style="margin: 30px 0;">
        <p><strong>Client Name:</strong> {{client_name}}</p>
        <p><strong>Date:</strong> {{current_date}}</p>
        <p><strong>Piercer:</strong> {{artist_name}}</p>
        <p><strong>Service:</strong> {{service_name}}</p>
    </div>
    
    <h2 style="color: #333; margin-top: 30px;">Consent and Acknowledgment</h2>
    
    <div style="line-height: 1.8; color: #333;">
        <p>I, <strong>{{client_name}}</strong>, hereby consent to receive piercing services from <strong>{{artist_name}}</strong> at Ouch Tattoo Studio.</p>
        
        <h3>I acknowledge and agree to the following:</h3>
        <ul>
            <li>I am at least 18 years of age and have provided valid identification.</li>
            <li>I am not under the influence of alcohol or drugs.</li>
            <li>I understand the risks including infection, allergic reactions, nerve damage, and excessive bleeding.</li>
            <li>I do not have any medical conditions that would prevent me from getting pierced.</li>
            <li>I have disclosed all relevant medical information and allergies.</li>
            <li>I understand that healing times vary and proper aftercare is essential.</li>
            <li>I will follow all aftercare instructions provided.</li>
            <li>I understand that jewelry should not be changed until the piercing is fully healed.</li>
            <li>I release Ouch Tattoo Studio and its piercers from any liability arising from this piercing.</li>
        </ul>
        
        <h3 style="margin-top: 20px;">Jewelry Agreement</h3>
        <p>I understand that only appropriate, high-quality jewelry will be used for my initial piercing and that I should not use inferior metals during healing.</p>
        
        <h3 style="margin-top: 20px;">Aftercare Commitment</h3>
        <p>I commit to following all aftercare instructions and will contact the studio if I experience any complications.</p>
    </div>
    
    <div style="margin-top: 50px; padding: 20px; background: #f5f5f5; border-radius: 5px;">
        <p style="margin: 0;"><strong>Booking ID:</strong> {{booking_id}}</p>
        <p style="margin: 10px 0 0 0;"><strong>Appointment Date:</strong> {{booking_date}} at {{booking_time}}</p>
    </div>
</div>
HTML;
    }

    private function getHealthWaiverContent(): string
    {
        return <<<HTML
<div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;">
    <h1 style="text-align: center; color: #333;">Health & Safety Waiver</h1>
    <p style="text-align: center; color: #666;">Ouch Tattoo Studio</p>
    
    <div style="margin: 30px 0;">
        <p><strong>Client Name:</strong> {{client_name}}</p>
        <p><strong>Email:</strong> {{client_email}}</p>
        <p><strong>Phone:</strong> {{client_phone}}</p>
        <p><strong>Date:</strong> {{current_date}}</p>
    </div>
    
    <h2 style="color: #333; margin-top: 30px;">Health Questionnaire</h2>
    
    <div style="line-height: 1.8; color: #333;">
        <p>Please ensure you have disclosed any of the following conditions:</p>
        
        <h3>Medical Conditions</h3>
        <ul>
            <li>Heart conditions or pacemaker</li>
            <li>Diabetes</li>
            <li>Hemophilia or blood clotting disorders</li>
            <li>HIV/AIDS or other immune system disorders</li>
            <li>Hepatitis</li>
            <li>Epilepsy or seizure disorders</li>
            <li>Skin conditions (eczema, psoriasis, etc.)</li>
            <li>Allergies to latex, metals, or inks</li>
        </ul>
        
        <h3 style="margin-top: 20px;">Current Medications</h3>
        <p>I have disclosed all medications I am currently taking, including blood thinners, antibiotics, and any other prescriptions or supplements.</p>
        
        <h3 style="margin-top: 20px;">Pregnancy</h3>
        <p>I confirm that I am not pregnant or breastfeeding, or I have consulted with my physician and received approval for this procedure.</p>
        
        <h3 style="margin-top: 20px;">Recent Procedures</h3>
        <p>I have disclosed any recent surgeries, vaccinations, or other body modifications within the last 6 months.</p>
        
        <h3 style="margin-top: 20px;">Liability Release</h3>
        <p>I understand that Ouch Tattoo Studio follows all health and safety regulations. I release the studio, its owners, and staff from any liability for complications arising from undisclosed medical conditions or failure to follow aftercare instructions.</p>
        
        <h3 style="margin-top: 20px;">Emergency Contact</h3>
        <p>I authorize the studio to seek emergency medical care on my behalf if necessary during my appointment.</p>
    </div>
    
    <div style="margin-top: 50px; padding: 20px; background: #f5f5f5; border-radius: 5px;">
        <p style="margin: 0;"><strong>Booking ID:</strong> {{booking_id}}</p>
        <p style="margin: 10px 0 0 0;"><strong>Appointment Date:</strong> {{booking_date}} at {{booking_time}}</p>
    </div>
    
    <div style="margin-top: 30px; padding: 20px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 5px;">
        <p style="margin: 0; font-weight: bold; color: #856404;">Important Notice</p>
        <p style="margin: 10px 0 0 0; color: #856404;">By signing this document, I confirm that all information provided is true and accurate to the best of my knowledge.</p>
    </div>
</div>
HTML;
    }
}