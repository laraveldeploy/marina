<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\File;

class ManageProject extends Page
{
    protected static string $resource = ProjectResource::class;

    protected static string $view = 'filament.resources.project-resource.pages.manage-project';

    protected $listeners = ['refreshComponent' => '$refresh'];

    protected $project;

    public $record;

    public $composeContent;

    public $isFolderCreated;

    public function mount($record)
    {
        $this->record = $record;
        $this->loadProject();
        $this->loadComposeContent();
        $this->bootFolderCreated();
    }

    protected function loadComposeContent()
    {
        // get the contents of the compose file
        $this->composeContent = File::get(
            storage_path('app/marina/' . $this->project->name . '/docker-compose.yml')
        );

    }

    protected function loadProject()
    {
        $this->project = Project::query()->find($this->record);
    }

    protected function bootFolderCreated()
    {
        $this->isFolderCreated = File::isDirectory(
            storage_path('app/marina/' . $this->project->name)
        );
    }

    public function createFolder()
    {
        $this->loadProject();

        File::ensureDirectoryExists(
            storage_path('app/marina/' . $this->project->name)
        );

        $this->isFolderCreated = true;
    }

    public function deleteFolder()
    {
        $this->loadProject();

        File::deleteDirectory(
            storage_path('app/marina/' . $this->project->name)
        );
        $this->isFolderCreated = false;
    }

    public function saveComposeFile()
    {
        $this->loadProject();

        $this->project->update([
            'compose_file' => $this->composeContent,
        ]);

        $result = File::put(
            storage_path('app/marina/' . $this->project->name . '/docker-compose.yml'),
            $this->composeContent
        );

        if($result === false) {
            Notification::make()
                ->danger()
                ->title('Failed to save docker-compose.yml file')
                ->send();
            return;
        }

        Notification::make()
            ->success()
            ->title('docker-compose.yml file saved successfully')
            ->send();
    }
}