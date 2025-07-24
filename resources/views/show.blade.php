@extends('sms-dev::layout')

@section('title', 'SMS Dev - Détail du SMS')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-3xl font-bold text-gray-900">📱 Détail du SMS</h1>
            
            <div class="flex items-center gap-2">
                <a href="/sms-dev"
                   class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors">
                    ← Retour à la liste
                </a>

                <a href="/sms-dev?action=delete-sms&id={{ $smsLog->id }}"
                   data-confirm="Supprimer ce SMS ?"
                   class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                    🗑️ Supprimer
                </a>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <!-- En-tête du SMS -->
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informations du SMS</h3>
                    
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-500 w-20">À :</span>
                            <span class="text-sm text-gray-900 font-mono">{{ $smsLog->to }}</span>
                        </div>
                        
                        @if($smsLog->from)
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-500 w-20">De :</span>
                            <span class="text-sm text-gray-900 font-mono">{{ $smsLog->from }}</span>
                        </div>
                        @endif
                        
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-500 w-20">Statut :</span>
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $smsLog->status === 'sent' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $smsLog->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $smsLog->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($smsLog->status) }}
                            </span>
                        </div>
                        
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-500 w-20">Lu :</span>
                            <span class="text-sm {{ $smsLog->is_read ? 'text-green-600' : 'text-red-600' }}">
                                {{ $smsLog->is_read ? '✅ Oui' : '❌ Non' }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Dates</h3>
                    
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-500 w-24">Créé le :</span>
                            <span class="text-sm text-gray-900">{{ $smsLog->created_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                        
                        @if($smsLog->sent_at)
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-500 w-24">Envoyé le :</span>
                            <span class="text-sm text-gray-900">{{ $smsLog->sent_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                        @endif
                        
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-500 w-24">Modifié le :</span>
                            <span class="text-sm text-gray-900">{{ $smsLog->updated_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenu du message -->
        <div class="px-6 py-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Contenu du message</h3>
            
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="whitespace-pre-wrap text-gray-900 leading-relaxed">{{ $smsLog->message }}</div>
            </div>
            
            <!-- Statistiques du message -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ strlen($smsLog->message) }}</div>
                    <div class="text-sm text-blue-800">Caractères</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-green-600">{{ str_word_count($smsLog->message) }}</div>
                    <div class="text-sm text-green-800">Mots</div>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ ceil(strlen($smsLog->message) / 160) }}</div>
                    <div class="text-sm text-purple-800">SMS (160 car.)</div>
                </div>
            </div>
        </div>

        <!-- Métadonnées -->
        @if($smsLog->metadata)
        <div class="border-t border-gray-200 px-6 py-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Métadonnées techniques</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if(isset($smsLog->metadata['notification_class']))
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Classe de notification</h4>
                    <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ $smsLog->metadata['notification_class'] }}</code>
                </div>
                @endif
                
                @if(isset($smsLog->metadata['notifiable_type']))
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Type de destinataire</h4>
                    <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ $smsLog->metadata['notifiable_type'] }}</code>
                </div>
                @endif
                
                @if(isset($smsLog->metadata['notifiable_id']))
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">ID du destinataire</h4>
                    <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ $smsLog->metadata['notifiable_id'] }}</code>
                </div>
                @endif
                
                @if(isset($smsLog->metadata['channel']))
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Canal</h4>
                    <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ $smsLog->metadata['channel'] }}</code>
                </div>
                @endif
            </div>
            
            <!-- Données complètes -->
            <div class="mt-6">
                <details class="group">
                    <summary class="cursor-pointer text-sm font-medium text-gray-700 hover:text-gray-900">
                        🔍 Voir toutes les métadonnées (JSON)
                    </summary>
                    <div class="mt-3 bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto">
                        <pre class="text-sm">{{ json_encode($smsLog->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </details>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
