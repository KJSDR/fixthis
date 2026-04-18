<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

use SuiteCRM\Custom\Dashboard\QuickStatsProvider;

class QuickStatsDashlet extends Dashlet
{
    public $title = 'CRM Quick Stats';
    public $description = 'At-a-glance counts of Contacts, Accounts, open Cases, and active Opportunities.';
    public $width = '100%';
    public $height = 120;

    public function __construct(string $id, array $def = [])
    {
        parent::__construct($id);
        $this->isConfigurable = false;
        $this->hasScript = false;
        if (!empty($def['title'])) {
            $this->title = $def['title'];
        }
    }

    public function display(): string
    {
        $provider = new QuickStatsProvider();
        $this->ss->assign('stats', $provider->getStats());
        return $this->ss->fetch(
            'custom/modules/Home/Dashlets/QuickStatsDashlet/QuickStatsDashlet.tpl'
        );
    }
}
