<?php 
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\Exportable; // ✅ Correctly imported
use Maatwebsite\Excel\Events\BeforeExport;

class Export implements FromView, WithEvents
{

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Load the view to be used for the export
     */
    public function view(): View
    {
        return view('account.view_all_fd_management', [
            'data' => $this->data
        ]);
    }

    /**
     * Register events to set Excel protection
     */
    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function (BeforeExport $event) {
                $event->writer->getDelegate()->getSecurity()->setLockWindows(true);
                $event->writer->getDelegate()->getSecurity()->setLockStructure(true);
                $event->writer->getDelegate()->getSecurity()->setWorkbookPassword('ABC123'); // Password
            },
        ];
    }
}
