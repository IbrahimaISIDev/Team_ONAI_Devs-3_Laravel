<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ArchiveDettesPayees;
use App\Services\ArchiveService;

class DispatchArchiveDettesPayees extends Command
{
    // La signature de la commande
    protected $signature = 'dispatch:archivedettespayees';
    
    // La description de la commande
    protected $description = 'Déclenche le job pour archiver les dettes payées';

    protected $archiveService;

    /**
     * Crée une nouvelle instance de commande.
     *
     * @param  \App\Services\ArchiveService  $archiveService
     * @return void
     */
    public function __construct(ArchiveService $archiveService)
    {
        parent::__construct();
        $this->archiveService = $archiveService;
    }

    /**
     * Exécute la commande.
     *
     * @return int
     */
    public function handle()
    {
        // Dispatcher le job avec l'instance d'ArchiveService injectée
        ArchiveDettesPayees::dispatch($this->archiveService);

        $this->info('Le job d\'archivage des dettes payées a été déclenché avec succès !');

        return 0; // Code de succès
    }
}
