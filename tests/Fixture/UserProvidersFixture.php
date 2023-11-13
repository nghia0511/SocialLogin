<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UserProvidersFixture
 */
class UserProvidersFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'user_id' => 1,
                'provider_name' => 'Lorem ipsum dolor sit amet',
                'provider_id' => 'Lorem ipsum dolor sit amet',
                'created_at' => 1699772930,
                'updated_at' => 1699772930,
            ],
        ];
        parent::init();
    }
}
