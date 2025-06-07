<?php

namespace App\Jobs;

use App\Models\UserRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AnalyzeUserJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public function __construct(protected UserRecord $record) {}

    public function handle(): void
    {
        // decodifica os dados integrados
        $data = json_decode($this->record->external_data, true);

        // lógica de risco
        $status = $data['cpf_status']['status'];
        $region = $data['viacep']['regiao'] ?? '';
        $risk   = ($status === 'negativado' && $region === 'Sudeste')
                  ? 'high_risk' : 'low_risk';

        // atualiza o registro
        $this->record->update(['risk_level' => $risk]);

        // gera PDF
        $pdf = Pdf::loadView('pdf.user_report', [
            'cpf'    => $this->record->cpf,
            'data'   => $data,
            'risk'   => $risk,
        ]);

        $filename = "user_report_{$this->record->cpf}.pdf";
        $path     = storage_path("app/reports/{$filename}");
        
        // Cria o diretório se não existir
        if (!file_exists(storage_path('app/reports'))) {
            mkdir(storage_path('app/reports'), 0755, true);
        }
        
        $pdf->save($path);

        // simula envio de email (log)
        Log::info('[AnalyzeUserJob] Simulated email to '.$this->record->email, [
            'report_path' => $path,
        ]);
    }
}
