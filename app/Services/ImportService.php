<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\Customer;
use App\Models\Product;

class ImportService
{
    protected $customerCsvPath = 'csv/customers.csv'; // Path within storage directory
    protected $productCsvPath = 'csv/products.csv';   // Path within storage directory

    public function import()
    {
        $customerImported = $this->importCustomers();
        $productImported = $this->importProducts();

        return [
            'customers_imported' => $customerImported,
            'products_imported' => $productImported,
        ];
    }

    protected function importCustomers()
    {
        $csvFile = storage_path($this->customerCsvPath);
        $importedCount = 0;

        if (($handle = fopen($csvFile, "r")) !== false) {
            
            // Skip the first row (header row)
            fgetcsv($handle, 1000, ',');
            
            while (($data = fgetcsv($handle, 100000, ",")) !== false) {
                $firstLast = explode(" ", $data[3]);
                $registered_since = date('Y-m-d', strtotime($data[4]));

                Customer::create([
                    'id' => $data[0],
                    'job_title' => $data[1],
                    'email' => $data[2],
                    'first_name' => $firstLast[0],
                    'last_name' => $firstLast[1],
                    'registered_since' => $registered_since,
                    'phone' => $data[5],
                ]);

                $importedCount++;
            }
            fclose($handle);
        }

        Log::info("Imported {$importedCount} customers.");
        return $importedCount;
    }

    protected function importProducts()
    {
        $csvFile = storage_path($this->productCsvPath);
        $importedCount = 0;

        if (($handle = fopen($csvFile, "r")) !== false) {
            
            // Skip the first row (header row)
            fgetcsv($handle, 1000, ',');
            while (($data = fgetcsv($handle, 100000, ",")) !== false) {
                Product::create([
                    'id' => $data[0],
                    'productname' => $data[1],
                    'price' => $data[2],
                ]);

                $importedCount++;
            }
            fclose($handle);
        }

        Log::info("Imported {$importedCount} products.");
        return $importedCount;
    }
}
