<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Expense;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class BudgetService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Calculate total spent for a project
     *
     * @param Project $project
     * @return float
     */
    public function calculateTotalSpent(Project $project): float
    {
        return $project->expenses()->sum('amount');
    }

    /**
     * Calculate remaining budget for a project
     *
     * @param Project $project
     * @return float
     */
    public function calculateRemainingBudget(Project $project): float
    {
        return max(0, $project->budget - $this->calculateTotalSpent($project));
    }

    /**
     * Calculate budget consumption percentage
     *
     * @param Project $project
     * @return float
     */
    public function calculateBudgetConsumption(Project $project): float
    {
        if ($project->budget <= 0) {
            return 0;
        }

        $totalSpent = $this->calculateTotalSpent($project);
        return ($totalSpent / $project->budget) * 100;
    }

    /**
     * Check if budget alert should be triggered (80% threshold)
     *
     * @param Project $project
     * @return bool
     */
    public function shouldTriggerBudgetAlert(Project $project): bool
    {
        // Only trigger if not already sent
        if ($project->budget_alert_sent) {
            return false;
        }

        $consumption = $this->calculateBudgetConsumption($project);
        return $consumption >= 80;
    }

    /**
     * Trigger budget alert notification
     *
     * @param Project $project
     * @return void
     */
    public function triggerBudgetAlert(Project $project): void
    {
        if ($this->shouldTriggerBudgetAlert($project)) {
            $this->notificationService->notifyBudgetAlert($project);
        }
    }

    /**
     * Add an expense to a project and check for budget alert
     *
     * @param Project $project
     * @param array $expenseData
     * @return Expense|null
     */
    public function addExpense(Project $project, array $expenseData): ?Expense
    {
        DB::beginTransaction();
        try {
            $expense = Expense::create([
                'project_id'       => $project->id,
                'description'      => $expenseData['description'],
                'amount'           => $expenseData['amount'],
                'attachement_date' => $expenseData['attachement_date'] ?? $expenseData['expense_date'] ?? now(),
            ]);

            // Check if we need to trigger budget alert
            $this->triggerBudgetAlert($project);

            DB::commit();
            return $expense;
        } catch (\Exception $e) {
            DB::rollBack();
            return null;
        }
    }

    /**
     * Get budget summary for a project
     *
     * @param Project $project
     * @return array
     */
    public function getBudgetSummary(Project $project): array
    {
        $totalSpent = $this->calculateTotalSpent($project);
        $remaining = $this->calculateRemainingBudget($project);
        $consumption = $this->calculateBudgetConsumption($project);

        return [
            'budget' => $project->budget,
            'total_spent' => $totalSpent,
            'remaining' => $remaining,
            'consumption_percent' => round($consumption, 2),
            'alert_triggered' => $project->budget_alert_sent,
            'is_over_budget' => $totalSpent > $project->budget,
        ];
    }

    /**
     * Get budget summary for a school
     *
     * @param int $schoolId
     * @return array
     */
    public function getSchoolBudgetSummary(int $schoolId): array
    {
        $school = \App\Models\School::find($schoolId);
        if (!$school) {
            return [];
        }

        $projects = $school->projects;
        $totalBudget = $school->annual_budget;
        $totalSpent = 0;

        foreach ($projects as $project) {
            $totalSpent += $this->calculateTotalSpent($project);
        }

        $remaining = max(0, $totalBudget - $totalSpent);
        $consumption = $totalBudget > 0 ? ($totalSpent / $totalBudget) * 100 : 0;

        return [
            'annual_budget' => $totalBudget,
            'total_spent' => $totalSpent,
            'remaining' => $remaining,
            'consumption_percent' => round($consumption, 2),
            'project_count' => $projects->count(),
            'projects_with_alerts' => $projects->where('budget_alert_sent', true)->count(),
        ];
    }

    /**
     * Check all projects for budget alerts
     * This can be run as a scheduled task
     *
     * @return int Number of alerts triggered
     */
    public function checkAllProjectsForAlerts(): int
    {
        $alertsTriggered = 0;

        Project::where('budget_alert_sent', false)
            ->where('status', '!=', 'Termine')
            ->chunk(100, function ($projects) use (&$alertsTriggered) {
                foreach ($projects as $project) {
                    if ($this->shouldTriggerBudgetAlert($project)) {
                        $this->notificationService->notifyBudgetAlert($project);
                        $alertsTriggered++;
                    }
                }
            });

        return $alertsTriggered;
    }
}
