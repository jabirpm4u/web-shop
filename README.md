# API Webshop

This project is a mini web shop API implemented using Laravel. It provides functionality to manage customers, products, and orders, and interacts with a micro payment provider to handle order payments.

## Installation

1. Clone the repository:
git clone https://github.com/jabirpm4u/web-shop.git
cd api-webshop


2. Install dependencies:
composer install


3. Create a `.env` file:
cp .env.example .env


4. Set up your database configurations in the `.env` file.

5. Generate an application key:
php artisan key:generate


6. Run database migrations and seeders:
php artisan migrate --seed


## Import Master Data

To import master data (customers and products), you can use the following artisan command:

php artisan import:master-data

This command will import data from the provided CSV files and log the import results.

## API Endpoints

- `GET /api/orders`: Retrieve a list of orders.
- `POST /api/orders`: Create a new order.
- `GET /api/orders/{id}`: Retrieve details of a specific order.
- `PUT /api/orders/{id}`: Update an existing order.
- `DELETE /api/orders/{id}`: Delete an order.
- `POST /api/orders/{id}/pay`: Pay an order using the Super Payment Provider.
- `GET /api/orders/{id}`: Retrieve details of a specific order.
- `GET /api/orders/{id}/products`: Get products that are added to a particular order.
- `POST /api/orders/{id}/add`: Attach a product to an order (order must be unpaid).

## Minimal API Documentation Using Postman

For a more detailed overview of the available endpoints and how to interact with the API, you can refer to the 
(https://documenter.getpostman.com/view/25159750/2s9Y5YRhao). This documentation provides examples of request and response structures for each endpoint.


## Usage

To test the APIs, you can import the Postman collection JSON file provided in the `storage/postman` directory. Follow these steps:

1. Open Postman.
2. Click on the "Import" button.
3. Choose "Import From File" and select the JSON file from the `storage/postman` directory.
4. The collection will be imported, and you can start testing the APIs using the predefined requests.


## Estimated and Tracked Time

### Estimated Time

- Initial planning & setup: 2 hours
- Import Master Data: 1 hour
- Create CRUD Operations for Orders: 2 hours
- Add Product to Order Endpoint: 1 hour
- List Order Products: 0.5 hour
- Pay Order Endpoint: 0.5 hour
- Handling Payment Provider: 0.5 hours
- API Testing: 2 hours
- Documentation: 1 hour

**Total Estimated Time:** 10.5 hours

### Tracked Time

- Initial planning & setup: 2 hours
- Import Master Data: 1.5 hour
- Create CRUD Operations for Orders: 3 hours
- Add Product to Order Endpoint: 1 hour
- List Order Products: 0.5 hour
- Pay Order Endpoint: 0.5 hour
- Handling Payment Provider: 0.5 hours
- API Testing: 2 hours
- Documentation: 1 hour

**Total Tracked Time:** 12 hours

## Contact

If you have any questions or feedback, feel free to contact me at jabirpm4u@gmail.com.

