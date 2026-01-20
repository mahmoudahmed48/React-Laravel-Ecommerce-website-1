<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->createUsers();

        $this->createCategories();

        $this->createProducts();

        $this->createOrders();

        $this->createOrderItems();

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('👤 Admin: admin@ecommerce.com / password123');
        $this->command->info('👤 Customer: customer@ecommerce.com / password123');
    }

    private function createUsers():void 
    {
        User::factory()->admin()->create([
            'email' => 'admin@ecommerce.com',
            'name'  => 'admin user'
        ]);

        User::factory()->customer()->create([
            'email' => 'customer@ecommerce.com',
            'name'  => 'Customer User'
        ]);

        User::factory(10)->customer()->create();
        $this->command->info('Created 12 Users');
    }

    private function createCategories():void 
    {
        $parentCategories = Category::factory(5)->parent()->create();

        foreach($parentCategories as $parent)
        {
            Category::factory(3)->child($parent->id)->create();
        }

        Category::factory(3)->parent()->create();

        $this->command->info('Created Categories');
    }

    private function createProducts():void 
    {
        $categories = Category::all();

        Product::factory(50)->create()->each(function ($product) use ($categories) {
            $product->category_id = $categories->random()->id;
            $product->save();
        });

        Product::factory(10)->featured()->create()->each(function ($product) use ($categories) {
            $product->category_id = $categories->random()->id;
            $product->save();         
        });

        Product::factory(5)->outOfStock()->create()->each(function ($product) use ($categories) {
            $product->category_id = $categories->random()->id;
            $product->save();         
        });

        $this->command->info('created 65 products');

        
    }

    private function createOrders():void 
    {
        $users = User::where('role', 'customer')->get();

        Order::factory(20)->make()->each(function ($order) use ($users) {
            $order->user_id = $users->random()->id;
            $order->save();
        });

        $this->command->info('created random 20 orders');
    }

    private function createOrderItems():void 
    {
        $orders = Order::all();
        $products = Product::all();

        foreach($orders as $order)
        {
            $itemsCount = rand(1,5);
            $selectedProducts = $products->random($itemsCount);

            foreach($selectedProducts as $product) 
            {
                OrderItem::factory()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'price' => $product->price,
                    'total' => $product->price * rand(1, 3),
                ]);
            }

            $order->total = $order->items->sum('total');
            $order->grand_total = $order->total + $order->shipping_cost + $order->tax - $order->discount;
            
            $order->save();
        }

        $this->command->info('Created Order Items');

    }


}
