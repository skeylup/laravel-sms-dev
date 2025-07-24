@extends('sms-dev::layout')

@section('title', 'SMS Dev - Boîte de réception')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header avec statistiques -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">📱 SMS Dev - Boîte de réception</h1>
        
        <!-- Statistiques -->
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</div>
                <div class="text-sm text-blue-800">Total</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg text-center">
                <div class="text-2xl font-bold text-green-600">{{ $stats['sent'] }}</div>
                <div class="text-sm text-green-800">Envoyés</div>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
                <div class="text-sm text-yellow-800">En attente</div>
            </div>
            <div class="bg-red-50 p-4 rounded-lg text-center">
                <div class="text-2xl font-bold text-red-600">{{ $stats['failed'] }}</div>
                <div class="text-sm text-red-800">Échoués</div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg text-center">
                <div class="text-2xl font-bold text-purple-600">{{ $stats['unread'] }}</div>
                <div class="text-sm text-purple-800">Non lus</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg text-center">
                <div class="text-2xl font-bold text-gray-600">{{ $stats['read'] }}</div>
                <div class="text-sm text-gray-800">Lus</div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="flex flex-wrap gap-2 mb-6">
            <a href="/sms-dev"
               class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                🔄 Actualiser
            </a>

            @if($stats['unread'] > 0)
            <a href="/sms-dev?action=mark-all-read"
               class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors">
                ✅ Marquer tout comme lu
            </a>
            @endif

            @if($stats['read'] > 0)
            <a href="/sms-dev?action=delete-read"
               data-confirm="Supprimer tous les SMS lus ?"
               class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600 transition-colors">
                🗑️ Supprimer les lus
            </a>
            @endif

            @if($stats['total'] > 0)
            <a href="/sms-dev?action=clear-all"
               data-confirm="Supprimer TOUS les SMS ?"
               class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                🗑️ Tout supprimer
            </a>
            @endif
        </div>

        <!-- Filtres -->
        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="status" class="border border-gray-300 rounded px-3 py-2">
                        <option value="">Tous</option>
                        <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Envoyés</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Échoués</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lecture</label>
                    <select name="read" class="border border-gray-300 rounded px-3 py-2">
                        <option value="">Tous</option>
                        <option value="0" {{ request('read') === '0' ? 'selected' : '' }}>Non lus</option>
                        <option value="1" {{ request('read') === '1' ? 'selected' : '' }}>Lus</option>
                    </select>
                </div>
                
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                    Filtrer
                </button>
                
                @if(request()->hasAny(['status', 'read']))
                <a href="/sms-dev"
                   class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors">
                    Réinitialiser
                </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Messages de succès -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        {{ session('success') }}
    </div>
    @endif

    <!-- Liste des SMS -->
    @if($smsLogs->count() > 0)
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        @foreach($smsLogs as $sms)
        <div class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
            <div class="p-4 flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <!-- Indicateur de lecture -->
                        <div class="w-3 h-3 rounded-full {{ $sms->is_read ? 'bg-gray-300' : 'bg-blue-500' }}"></div>
                        
                        <!-- Numéro de téléphone -->
                        <span class="font-medium text-gray-900">{{ $sms->to }}</span>
                        
                        <!-- Statut -->
                        <span class="px-2 py-1 text-xs rounded-full
                            {{ $sms->status === 'sent' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $sms->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $sms->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($sms->status) }}
                        </span>
                        
                        <!-- Date -->
                        <span class="text-sm text-gray-500">
                            {{ $sms->sent_at ? $sms->sent_at->format('d/m/Y H:i') : $sms->created_at->format('d/m/Y H:i') }}
                        </span>
                    </div>
                    
                    <!-- Aperçu du message -->
                    <p class="text-gray-700 {{ $sms->is_read ? '' : 'font-medium' }}">
                        {{ $sms->message_preview }}
                    </p>
                    
                    <!-- Classe de notification -->
                    @if($sms->notification_class)
                    <p class="text-xs text-gray-500 mt-1">
                        📧 {{ class_basename($sms->notification_class) }}
                    </p>
                    @endif
                </div>
                
                <!-- Actions -->
                <div class="flex items-center gap-2 ml-4">
                    <a href="/sms-dev/{{ $sms->id }}/show"
                       class="px-3 py-1 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 transition-colors">
                        Voir
                    </a>

                    <a href="/sms-dev?action=delete-sms&id={{ $sms->id }}"
                       data-confirm="Supprimer ce SMS ?"
                       class="px-3 py-1 bg-red-500 text-white text-sm rounded hover:bg-red-600 transition-colors">
                        🗑️
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $smsLogs->withQueryString()->links() }}
    </div>
    @else
    <div class="text-center py-12">
        <div class="text-6xl mb-4">📭</div>
        <h3 class="text-xl font-medium text-gray-900 mb-2">Aucun SMS trouvé</h3>
        <p class="text-gray-500">Les SMS interceptés apparaîtront ici.</p>
    </div>
    @endif
</div>
@endsection
