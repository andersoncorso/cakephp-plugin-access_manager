<?php
namespace AccessManager\Test\TestCase\Model\Table;

use AccessManager\Model\Table\ProfilesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * AccessManager\Model\Table\ProfilesTable Test Case
 */
class ProfilesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \AccessManager\Model\Table\ProfilesTable
     */
    public $Profiles;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.access_manager.profiles',
        'plugin.access_manager.users',
        'plugin.access_manager.aros',
        'plugin.access_manager.acos',
        'plugin.access_manager.permissions',
        'plugin.access_manager.groups',
        'plugin.access_manager.roles',
        'plugin.access_manager.estados',
        'plugin.access_manager.municipios'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Profiles') ? [] : ['className' => ProfilesTable::class];
        $this->Profiles = TableRegistry::get('Profiles', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Profiles);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
