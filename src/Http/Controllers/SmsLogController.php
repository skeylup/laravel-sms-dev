<?php

namespace Skeylup\LaravelSmsDev\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Skeylup\LaravelSmsDev\Models\SmsLog;

class SmsLogController
{
    /**
     * Afficher la liste des SMS avec vue combinée et gérer les actions
     */
    public function index(Request $request): View|RedirectResponse
    {
        // Gérer les actions
        if ($request->filled('action')) {
            return $this->handleAction($request->get('action'), $request);
        }

        $query = SmsLog::query()->latest();

        // Filtrer par statut si demandé
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Filtrer par statut de lecture si demandé
        if ($request->filled('read')) {
            if ($request->boolean('read')) {
                $query->read();
            } else {
                $query->unread();
            }
        }

        $smsLogs = $query->paginate(50); // Plus de SMS dans la sidebar
        $stats = $this->getStats();

        // SMS sélectionné (le premier par défaut ou celui demandé)
        $selectedSms = null;
        if ($request->filled('sms')) {
            $selectedSms = SmsLog::find($request->get('sms'));
        } elseif ($smsLogs->count() > 0) {
            $selectedSms = $smsLogs->first();
        }

        // Marquer comme lu automatiquement si sélectionné
        if ($selectedSms && ! $selectedSms->is_read) {
            $selectedSms->markAsRead();
        }

        return view('sms-dev::mailbox', compact('smsLogs', 'stats', 'selectedSms'));
    }

    /**
     * Afficher un SMS spécifique
     */
    public function show(SmsLog $smsLog): View
    {
        // Marquer comme lu automatiquement
        if (! $smsLog->is_read) {
            $smsLog->markAsRead();
        }

        return view('sms-dev::show', compact('smsLog'));
    }

    /**
     * Gérer les actions via paramètre GET
     */
    protected function handleAction(string $action, Request $request): RedirectResponse
    {
        switch ($action) {
            case 'clear-all':
                $count = SmsLog::count();
                SmsLog::truncate();

                return redirect()->route('sms-dev.index')
                    ->with('success', "All SMS messages have been deleted ({$count} total).");

            case 'mark-all-read':
                $count = SmsLog::unread()->count();
                SmsLog::unread()->update(['is_read' => true]);

                return redirect()->route('sms-dev.index')
                    ->with('success', "{$count} SMS messages marked as read.");

            case 'delete-read':
                $count = SmsLog::read()->count();
                SmsLog::read()->delete();

                return redirect()->route('sms-dev.index')
                    ->with('success', "{$count} read SMS messages have been deleted.");

            case 'delete-sms':
                if ($request->filled('id')) {
                    $sms = SmsLog::find($request->get('id'));
                    if ($sms) {
                        $sms->delete();

                        return redirect()->route('sms-dev.index')
                            ->with('success', 'SMS message deleted successfully.');
                    }
                }

                return redirect()->route('sms-dev.index')
                    ->with('error', 'SMS message not found.');

            default:
                return redirect()->route('sms-dev.index');
        }
    }

    /**
     * Obtenir les statistiques des SMS
     */
    protected function getStats(): array
    {
        return [
            'total' => SmsLog::count(),
            'unread' => SmsLog::unread()->count(),
            'read' => SmsLog::read()->count(),
            'sent' => SmsLog::byStatus('sent')->count(),
            'pending' => SmsLog::byStatus('pending')->count(),
            'failed' => SmsLog::byStatus('failed')->count(),
        ];
    }
}
