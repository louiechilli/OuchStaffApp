@extends('layouts.app')

@section('title', 'Client Details')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-2xl font-bold mb-6">Client Details</h1>

    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <p><strong>Name:</strong> {{ $client->name ?? $client->first_name . ' ' . $client->last_name }}</p>
        <p><strong>Email:</strong> {{ $client->email }}</p>
        <p><strong>Phone:</strong> {{ $client->phone }}</p>
        <p><strong>Created At:</strong> {{ $client->created_at->format('d M Y H:i') }}</p>
    </div>
</div>
@endsection