<?php

namespace SuiteCRM\Custom\Dashboard;

class QuickStatsProvider
{
    public function __construct(private mixed $db = null) {}

    private function getDb(): mixed
    {
        if ($this->db === null) {
            $this->db = \DBManagerFactory::getInstance();
        }
        return $this->db;
    }

    public function getStats(): array
    {
        return [
            'contacts'             => $this->countContacts(),
            'accounts'             => $this->countAccounts(),
            'open_cases'           => $this->countOpenCases(),
            'active_opportunities' => $this->countActiveOpportunities(),
        ];
    }

    public function countContacts(): int
    {
        return $this->runCountQuery('SELECT COUNT(*) AS c FROM contacts WHERE deleted = 0');
    }

    public function countAccounts(): int
    {
        return $this->runCountQuery('SELECT COUNT(*) AS c FROM accounts WHERE deleted = 0');
    }

    public function countOpenCases(): int
    {
        return $this->runCountQuery("SELECT COUNT(*) AS c FROM cases WHERE deleted = 0 AND status != 'Closed'");
    }

    public function countActiveOpportunities(): int
    {
        return $this->runCountQuery("SELECT COUNT(*) AS c FROM opportunities WHERE deleted = 0 AND sales_stage NOT IN ('Closed Won', 'Closed Lost')");
    }

    private function runCountQuery(string $sql): int
    {
        $db = $this->getDb();
        $result = $db->query($sql);
        $row = $db->fetchByAssoc($result);
        return (int) ($row['c'] ?? 0);
    }
}
