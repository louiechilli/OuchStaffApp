<div>
    <label for="artist_id" class="block text-sm font-medium text-slate-700 mb-1">Artist</label>
    <select id="artist_id" name="artist_id"
            class="w-full rounded-2xl border border-slate-200 bg-white p-4 md:p-6 shadow-sm"
            x-on:change="$dispatch('artist-selected', { id: $event.target.value, name: $event.target.options[$event.target.selectedIndex].text })">
        <option value="" selected disabled>Choose an artist…</option>
        @foreach($artists as $artist)
            <option value="{{ $artist->id }}">{{ $artist->first_name }} {{ $artist->last_name }}</option>
        @endforeach
    </select>
</div>
