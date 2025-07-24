<?php

namespace Skeylup\LaravelSmsDev\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Skeylup\LaravelSmsDev\Models\SmsLog;
use Skeylup\LaravelSmsDev\Notifications\TestSmsNotification;

class LaravelSmsDevCommand extends Command
{
    public $signature = 'sms-dev {action=stats : Action à effectuer (stats|test|cleanup|clear)}
                        {--user= : ID de l\'utilisateur pour le test}
                        {--phone= : Numéro de téléphone pour le test}
                        {--message= : Message personnalisé pour le test}
                        {--days=30 : Nombre de jours pour le nettoyage}';

    public $description = 'Utility commands for SMS Dev';

    public function handle(): int
    {
        $action = $this->argument('action');

        return match ($action) {
            'stats' => $this->showStats(),
            'test' => $this->sendTest(),
            'cleanup' => $this->cleanup(),
            'clear' => $this->clear(),
            default => $this->showHelp(),
        };
    }

    protected function showStats(): int
    {
        $this->info('📊 SMS Dev Statistics');
        $this->line('');

        $total = SmsLog::count();
        $unread = SmsLog::unread()->count();
        $read = SmsLog::read()->count();
        $sent = SmsLog::byStatus('sent')->count();
        $pending = SmsLog::byStatus('pending')->count();
        $failed = SmsLog::byStatus('failed')->count();

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total SMS', $total],
                ['Unread', $unread],
                ['Read', $read],
                ['Sent', $sent],
                ['Pending', $pending],
                ['Failed', $failed],
            ]
        );

        if ($total > 0) {
            $latest = SmsLog::latest()->first();
            $this->line('');
            $this->info("Latest SMS: {$latest->created_at->diffForHumans()}");
            $this->line("Recipient: {$latest->to}");
            $this->line('Message: '.substr($latest->message, 0, 50).'...');
        }

        return self::SUCCESS;
    }

    protected function sendTest(): int
    {
        $this->info('📱 Envoi d\'un SMS de test');

        // Créer un utilisateur fictif ou utiliser un existant
        if ($userId = $this->option('user')) {
            $user = User::find($userId);
            if (! $user) {
                $this->error("Utilisateur avec l'ID {$userId} introuvable.");

                return self::FAILURE;
            }
        } else {
            // Créer un objet anonyme avec un numéro de téléphone
            $phone = $this->option('phone') ?? '+33123456789';
            $user = new class($phone)
            {
                use \Illuminate\Notifications\Notifiable;

                public function __construct(public string $phone) {}

                public function getKey()
                {
                    return 'test';
                }

                public function routeNotificationForSms()
                {
                    return $this->phone;
                }
            };
        }

        $message = $this->option('message') ?? 'SMS de test envoyé via la commande sms-dev!';

        try {
            $user->notify(new TestSmsNotification($message));
            $this->info('✅ SMS de test envoyé avec succès!');
            $this->line('Destinataire: '.($this->option('phone') ?? '+33123456789'));
            $this->line("Message: {$message}");
        } catch (\Exception $e) {
            $this->error("❌ Erreur lors de l'envoi: ".$e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    protected function cleanup(): int
    {
        $days = (int) $this->option('days');
        $this->info("🧹 Nettoyage des SMS de plus de {$days} jours");

        $count = SmsLog::where('created_at', '<', now()->subDays($days))->count();

        if ($count === 0) {
            $this->info('Aucun SMS à nettoyer.');

            return self::SUCCESS;
        }

        if ($this->confirm("Supprimer {$count} SMS de plus de {$days} jours ?")) {
            SmsLog::where('created_at', '<', now()->subDays($days))->delete();
            $this->info("✅ {$count} SMS supprimés.");
        } else {
            $this->info('Nettoyage annulé.');
        }

        return self::SUCCESS;
    }

    protected function clear(): int
    {
        $count = SmsLog::count();

        if ($count === 0) {
            $this->info('Aucun SMS à supprimer.');

            return self::SUCCESS;
        }

        $this->warn("⚠️  Vous êtes sur le point de supprimer TOUS les SMS ({$count} au total)");

        if ($this->confirm('Êtes-vous sûr de vouloir continuer ?')) {
            SmsLog::truncate();
            $this->info("✅ Tous les SMS ont été supprimés ({$count} au total).");
        } else {
            $this->info('Suppression annulée.');
        }

        return self::SUCCESS;
    }

    protected function showHelp(): int
    {
        $this->info('📱 SMS Dev - Commandes disponibles');
        $this->line('');

        $this->table(
            ['Commande', 'Description'],
            [
                ['sms-dev stats', 'Afficher les statistiques'],
                ['sms-dev test', 'Envoyer un SMS de test'],
                ['sms-dev cleanup', 'Nettoyer les anciens SMS'],
                ['sms-dev clear', 'Supprimer tous les SMS'],
            ]
        );

        $this->line('');
        $this->info('Options pour le test:');
        $this->line('  --user=ID     : ID de l\'utilisateur destinataire');
        $this->line('  --phone=NUM   : Numéro de téléphone destinataire');
        $this->line('  --message=MSG : Message personnalisé');

        $this->line('');
        $this->info('Options pour le nettoyage:');
        $this->line('  --days=30     : Nombre de jours (défaut: 30)');

        return self::SUCCESS;
    }
}
