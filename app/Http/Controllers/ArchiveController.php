<?php

namespace App\Http\Controllers;

use App\Models\Project;

class ArchiveController extends Controller
{
    public function export()
    {
        $user  = auth()->user();
        $query = Project::where('status', 'Termine')
            ->with('school', 'nature', 'expenses')
            ->orderBy('closed_at', 'desc');

        if ($user->role === 'directeur_ecole') {
            $query->where('school_id', $user->school_id);
        }

        $projects = $query->get();
        $filename = 'archives-' . now()->format('Y-m-d') . '.xls';

        return response($this->buildHtml($projects), 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
            'Pragma'              => 'public',
        ]);
    }

    private function buildHtml($projects): string
    {
        $h   = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $fmt = fn($n)  => number_format((float) $n, 0, ',', ' ');

        $rows = '';
        foreach ($projects as $i => $p) {
            $spent    = (float) $p->expenses->sum('amount');
            $ecart    = (float) $p->budget - $spent;
            $alt      = $i % 2 === 1 ? 'background-color:#F8FAFC;' : '';
            $spentCss = $spent > $p->budget ? 'color:#DC2626;font-weight:bold;' : 'font-weight:bold;';
            $ecartCss = $ecart >= 0
                ? 'color:#16A34A;font-weight:bold;'
                : 'color:#DC2626;font-weight:bold;';
            $ecartVal = ($ecart >= 0 ? '+' : '') . $fmt($ecart);

            $rows .= '<tr style="' . $alt . '">'
                . '<td style="text-align:center;">' . ($i + 1) . '</td>'
                . '<td>' . $h($p->title_project) . '</td>'
                . '<td>' . $h($p->school->name_school) . '</td>'
                . '<td>' . $h($p->nature->name_nature ?? '-') . '</td>'
                . '<td style="text-align:center;">' . $h($p->type_project) . '</td>'
                . '<td style="text-align:right;font-weight:bold;">' . $fmt($p->budget) . '</td>'
                . '<td style="text-align:right;' . $spentCss . '">' . $fmt($spent) . '</td>'
                . '<td style="text-align:right;' . $ecartCss . '">' . $ecartVal . '</td>'
                . '<td style="text-align:center;">' . $h($p->closed_at?->format('d/m/Y') ?? '-') . '</td>'
                . '</tr>' . "\n";
        }

        return '<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta charset="UTF-8">
<!--[if gte mso 9]><xml>
  <x:ExcelWorkbook>
    <x:ExcelWorksheets><x:ExcelWorksheet>
      <x:Name>Archives</x:Name>
      <x:WorksheetOptions>
        <x:FreezePanes/>
        <x:FrozenNoSplit/>
        <x:SplitHorizontal>1</x:SplitHorizontal>
        <x:TopRowBottomPane>1</x:TopRowBottomPane>
      </x:WorksheetOptions>
    </x:ExcelWorksheet></x:ExcelWorksheets>
  </x:ExcelWorkbook>
</xml><![endif]-->
<style>
  body  { font-family: Calibri, Arial, sans-serif; font-size: 10pt; }
  table { border-collapse: collapse; width: 100%; }
  th    {
    background-color: #0F172A;
    color: #FFFFFF;
    font-weight: bold;
    font-size: 11pt;
    padding: 10px 14px;
    text-align: center;
    border: 1px solid #1E293B;
    white-space: nowrap;
  }
  td    { padding: 7px 12px; border: 1px solid #E2E8F0; font-size: 10pt; vertical-align: middle; }
</style>
</head>
<body>
<table>
<thead>
<tr>
  <th style="width:40px">N°</th>
  <th style="width:230px">Titre du projet</th>
  <th style="width:140px">École</th>
  <th style="width:130px">Nature</th>
  <th style="width:100px">Type</th>
  <th style="width:120px">Budget (KDA)</th>
  <th style="width:120px">Dépensé (KDA)</th>
  <th style="width:120px">Écart (KDA)</th>
  <th style="width:100px">Date clôture</th>
</tr>
</thead>
<tbody>
' . $rows . '</tbody>
</table>
</body>
</html>';
    }
}
