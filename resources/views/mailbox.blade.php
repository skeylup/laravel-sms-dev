@extends('sms-dev::layout')

@section('title', 'SMS Dev - Inbox')

@section('content')
<div class="h-screen flex flex-col bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <h1 class="text-2xl font-bold text-gray-900">📱 SMS Dev</h1>
                
                <!-- Compact statistics -->
                <div class="flex items-center space-x-4 text-sm">
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                        {{ $stats['total'] }} total
                    </span>
                    @if($stats['unread'] > 0)
                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full">
                        {{ $stats['unread'] }} unread
                    </span>
                    @endif
                </div>
            </div>
            
            <!-- Quick actions -->
            <div class="flex items-center space-x-2">
                <a href="/sms-dev"
                   class="px-3 py-1 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 transition-colors">
                    🔄 Refresh
                </a>

                @if($stats['unread'] > 0)
                <a href="/sms-dev?action=mark-all-read"
                   class="px-3 py-1 bg-green-500 text-white text-sm rounded hover:bg-green-600 transition-colors">
                    ✅ Mark all read
                </a>
                @endif

                @if($stats['total'] > 0)
                <a href="/sms-dev?action=clear-all"
                   class="px-3 py-1 bg-red-500 text-white text-sm rounded hover:bg-red-600 transition-colors"
                   data-confirm="Delete ALL SMS messages?">
                    🗑️ Clear all
                </a>
                @endif
            </div>
        </div>
        
        <!-- Filters -->
        @if($stats['total'] > 0)
        <div class="mt-4 flex items-center space-x-4">
            <form method="GET" class="flex items-center space-x-2">
                <select name="status" class="text-sm border border-gray-300 rounded px-2 py-1">
                    <option value="">All statuses</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>

                <select name="read" class="text-sm border border-gray-300 rounded px-2 py-1">
                    <option value="">All</option>
                    <option value="0" {{ request('read') === '0' ? 'selected' : '' }}>Unread</option>
                    <option value="1" {{ request('read') === '1' ? 'selected' : '' }}>Read</option>
                </select>

                <button type="submit" class="px-3 py-1 bg-gray-500 text-white text-sm rounded hover:bg-gray-600 transition-colors">
                    Filter
                </button>

                @if(request()->hasAny(['status', 'read']))
                <a href="/sms-dev"
                   class="px-3 py-1 bg-gray-300 text-gray-700 text-sm rounded hover:bg-gray-400 transition-colors">
                    Reset
                </a>
                @endif
            </form>
        </div>
        @endif
    </div>

    <!-- Messages de succès -->
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mx-6 mt-4 rounded">
        {{ session('success') }}
    </div>
    @endif

    <!-- Main content -->
    <div class="flex-1 flex overflow-hidden">
        <!-- Sidebar - SMS List -->
        <div class="w-1/3 bg-white border-r border-gray-200 flex flex-col">
            <!-- Sidebar header -->
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h2 class="font-medium text-gray-900">Messages ({{ $smsLogs->total() }})</h2>
            </div>
            
            <!-- SMS List -->
            <div class="flex-1 overflow-y-auto sms-sidebar">
                @if($smsLogs->count() > 0)
                    @foreach($smsLogs as $sms)
                    <a href="/sms-dev?{{ http_build_query(array_merge(request()->query(), ['sms' => $sms->id])) }}"
                       class="sms-item block px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition-colors {{ $selectedSms && $selectedSms->id === $sms->id ? 'bg-blue-50 border-l-4 border-l-blue-500' : '' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <!-- Indicateur + Destinataire -->
                                <div class="flex items-center space-x-2 mb-1">
                                    <div class="w-2 h-2 rounded-full {{ $sms->is_read ? 'bg-gray-300' : 'bg-blue-500' }}"></div>
                                    <div class="flex-1 min-w-0">
                                        <span class="text-sm font-medium text-gray-900 truncate">{{ $sms->to }}</span>
                                        @if(isset($sms->metadata['notifiable_name']) || isset($sms->metadata['notifiable_email']))
                                        <div class="text-xs text-gray-500 truncate">
                                            @if(isset($sms->metadata['notifiable_name']))
                                                {{ $sms->metadata['notifiable_name'] }}
                                            @endif
                                            @if(isset($sms->metadata['notifiable_id']))
                                                (ID: {{ $sms->metadata['notifiable_id'] }})
                                            @endif
                                            @if(isset($sms->metadata['notifiable_email']))
                                                • {{ $sms->metadata['notifiable_email'] }}
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                    <span class="text-xs px-1 py-0.5 rounded {{ $sms->status === 'sent' ? 'bg-green-100 text-green-800' : ($sms->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($sms->status) }}
                                    </span>
                                </div>
                                
                                <!-- Aperçu du message -->
                                <p class="text-sm text-gray-600 truncate {{ $sms->is_read ? '' : 'font-medium' }}">
                                    {{ $sms->message_preview }}
                                </p>
                                
                                <!-- Date et classe -->
                                <div class="flex items-center justify-between mt-1">
                                    <span class="text-xs text-gray-500">
                                        {{ $sms->sent_at ? $sms->sent_at->format('H:i') : $sms->created_at->format('H:i') }}
                                    </span>
                                    @if($sms->notification_class)
                                    <span class="text-xs text-gray-400">
                                        {{ class_basename($sms->notification_class) }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                    
                    <!-- Pagination -->
                    @if($smsLogs->hasPages())
                    <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                        {{ $smsLogs->withQueryString()->simplePaginate() }}
                    </div>
                    @endif
                @else
                    <div class="flex-1 flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-4xl mb-2">📭</div>
                            <p class="text-gray-500">No SMS</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Main content - SMS Detail -->
        <div class="flex-1 flex flex-col bg-white">
            @if($selectedSms)
                <!-- Message header -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">SMS to {{ $selectedSms->to }}</h3>
                            @if(isset($selectedSms->metadata['notifiable_name']) || isset($selectedSms->metadata['notifiable_email']))
                            <div class="text-sm text-gray-600 mt-1">
                                Recipient:
                                @if(isset($selectedSms->metadata['notifiable_name']))
                                    <span class="font-medium">{{ $selectedSms->metadata['notifiable_name'] }}</span>
                                @endif
                                @if(isset($selectedSms->metadata['notifiable_id']))
                                    <span class="text-gray-500">(ID: {{ $selectedSms->metadata['notifiable_id'] }})</span>
                                @endif
                                @if(isset($selectedSms->metadata['notifiable_email']))
                                    <span class="text-blue-600">{{ $selectedSms->metadata['notifiable_email'] }}</span>
                                @endif
                            </div>
                            @endif
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $selectedSms->sent_at ? $selectedSms->sent_at->format('m/d/Y at H:i:s') : $selectedSms->created_at->format('m/d/Y at H:i:s') }}
                                @if($selectedSms->from)
                                • From: {{ $selectedSms->from }}
                                @endif
                            </p>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 text-xs rounded-full {{ $selectedSms->status === 'sent' ? 'bg-green-100 text-green-800' : ($selectedSms->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($selectedSms->status) }}
                            </span>
                            
                            <a href="/sms-dev?action=delete-sms&id={{ $selectedSms->id }}"
                               class="px-3 py-1 bg-red-500 text-white text-sm rounded hover:bg-red-600 transition-colors"
                               data-confirm="Delete this SMS?">
                                🗑️ Delete
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Message content -->
                <div class="flex-1 p-6 overflow-y-auto">
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <div class="whitespace-pre-wrap text-gray-900 leading-relaxed">{{ $selectedSms->message }}</div>
                    </div>

                    <!-- Message statistics -->
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="bg-blue-50 p-3 rounded-lg text-center">
                            <div class="text-lg font-bold text-blue-600">{{ strlen($selectedSms->message) }}</div>
                            <div class="text-xs text-blue-800">Characters</div>
                        </div>
                        <div class="bg-green-50 p-3 rounded-lg text-center">
                            <div class="text-lg font-bold text-green-600">{{ str_word_count($selectedSms->message) }}</div>
                            <div class="text-xs text-green-800">Words</div>
                        </div>
                        <div class="bg-purple-50 p-3 rounded-lg text-center">
                            <div class="text-lg font-bold text-purple-600">{{ ceil(strlen($selectedSms->message) / 160) }}</div>
                            <div class="text-xs text-purple-800">SMS (160 chars)</div>
                        </div>
                    </div>

                    <!-- Metadata -->
                    @if($selectedSms->metadata)
                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Technical metadata</h4>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            @if(isset($selectedSms->metadata['notification_class']))
                            <div>
                                <span class="text-gray-500">Notification:</span>
                                <code class="ml-2 bg-gray-100 px-2 py-1 rounded text-xs">{{ class_basename($selectedSms->metadata['notification_class']) }}</code>
                            </div>
                            @endif

                            @if(isset($selectedSms->metadata['notifiable_type']))
                            <div>
                                <span class="text-gray-500">Recipient type:</span>
                                <code class="ml-2 bg-gray-100 px-2 py-1 rounded text-xs">{{ class_basename($selectedSms->metadata['notifiable_type']) }}</code>
                            </div>
                            @endif

                            @if(isset($selectedSms->metadata['notifiable_id']))
                            <div>
                                <span class="text-gray-500">Recipient ID:</span>
                                <code class="ml-2 bg-gray-100 px-2 py-1 rounded text-xs">{{ $selectedSms->metadata['notifiable_id'] }}</code>
                            </div>
                            @endif

                            @if(isset($selectedSms->metadata['notifiable_name']))
                            <div>
                                <span class="text-gray-500">Recipient name:</span>
                                <code class="ml-2 bg-gray-100 px-2 py-1 rounded text-xs">{{ $selectedSms->metadata['notifiable_name'] }}</code>
                            </div>
                            @endif

                            @if(isset($selectedSms->metadata['notifiable_email']))
                            <div>
                                <span class="text-gray-500">Recipient email:</span>
                                <code class="ml-2 bg-gray-100 px-2 py-1 rounded text-xs">{{ $selectedSms->metadata['notifiable_email'] }}</code>
                            </div>
                            @endif

                            @if(isset($selectedSms->metadata['channel']))
                            <div>
                                <span class="text-gray-500">Channel:</span>
                                <code class="ml-2 bg-gray-100 px-2 py-1 rounded text-xs">{{ $selectedSms->metadata['channel'] }}</code>
                            </div>
                            @endif
                        </div>

                        <!-- Complete JSON -->
                        <details class="mt-4">
                            <summary class="cursor-pointer text-sm text-gray-600 hover:text-gray-900">
                                🔍 View all metadata (JSON)
                            </summary>
                            <div class="mt-2 bg-gray-900 text-green-400 p-3 rounded text-xs overflow-x-auto">
                                <pre>{{ json_encode($selectedSms->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </details>
                    </div>
                    @endif
                </div>
            @else
                <!-- No SMS selected -->
                <div class="flex-1 flex items-center justify-center">
                    <div class="text-center">
                        <div class="text-6xl mb-4">📱</div>
                        <h3 class="text-xl font-medium text-gray-900 mb-2">Select an SMS</h3>
                        <p class="text-gray-500">Choose an SMS from the list to view its content</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
