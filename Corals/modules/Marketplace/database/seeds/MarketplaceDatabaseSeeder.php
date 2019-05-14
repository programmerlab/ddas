<?php

namespace Corals\Modules\Marketplace\database\seeds;

use Carbon\Carbon;
use Corals\Menu\Models\Menu;
use Corals\Modules\Ecommerce\Models\Order;
use Corals\Modules\Payment\Models\Invoice;
use Corals\Modules\Subscriptions\Models\Product;
use Corals\Settings\Models\Setting;
use Corals\User\Models\Permission;
use Corals\User\Models\Role;
use Illuminate\Database\Seeder;

class MarketplaceDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(MarketplacePermissionsDatabaseSeeder::class);
        $this->call(MarketplaceMenuDatabaseSeeder::class);
        $this->call(MarketplaceDefaultsDatabaseSeeder::class);
        $this->call(MarketplaceNotificationTemplatesSeeder::class);
        $this->call(MarketplaceSubscriptionProductDatabaseSeeder::class);

        \DB::table('marketplace_stores')->insertGetId([
            'id' => 1,
            'name' => 'Main Store',
            'slug' => '',
            'parking_domain' => '',
            'user_id' => '1',// admin
            'status' => 'active'
        ]);

        \DB::table('settings')->insert([
            [
                'code' => 'supported_shipping_methods',
                'type' => 'SELECT',
                'category' => 'Marketplace',
                'label' => 'Supported Shipping methods',
                'value' => json_encode(['FlatRate' => 'Flat Rate', 'Shippo' => 'Shippo', 'Free' => 'Free']),
                'editable' => 1,
                'hidden' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);


    }

    public function rollback()
    {
        Permission::where('name', 'like', 'Marketplace%')->delete();
        Permission::where('name', 'Administrations::admin.marketplace')->delete();

        Setting::where('code', 'supported_shipping_methods')->delete();

        $menus = Menu::where('key', 'marketplace')->get();

        foreach ($menus as $menu) {
            Menu::where('parent_id', $menu->id)->delete();
            $menu->delete();
        }
        Role::whereName('vendor')->delete();
        Product::whereName('Marketplace Subscription Product')->delete();
        Invoice::where('invoicable_type', Order::class)->delete();

        Setting::where('code', 'like', 'marketplace_%')->delete();
    }
}
