DropEx

DropEx is a global shipping and logistics platform designed to provide reliable, fast, and seamless shipping solutions to meet the demands of an interconnected world.

Table of Contents

Features

Demo

Installation

Usage

Technologies Used

Contributing

License

Contact


Features

Global Reach: Connects major hubs and remote locations worldwide.

Fast and Secure: Precision handling with guaranteed safety.

24/7 Support: Dedicated team for continuous assistance.

Eco-Friendly: Committed to sustainable logistics.


Demo

A live demo of the application is available at: DropEx Live Demo

Installation

To set up the project locally, follow these steps:

1. Clone the Repository:

git clone https://github.com/your-username/dropex.git
cd dropex


2. Backend Setup:

Navigate to the backend directory:

cd backend

Install dependencies:

npm install

Set up environment variables:

Create a .env file based on the provided .env.example.

Configure your database connection and other environment variables as needed.


Run database migrations (if applicable):

npm run migrate

Start the backend server:

npm start



3. Frontend Setup:

Navigate to the frontend directory:

cd ../frontend

Install dependencies:

npm install

Set up environment variables:

Create a .env file based on the provided .env.example.

Ensure the API endpoint is correctly configured to communicate with the backend server.


Start the frontend development server:

npm start




Usage

Once both the backend and frontend servers are running:

Access the application at http://localhost:3000.

Register as a new user or log in with existing credentials.

Explore services such as Air Freight, Road Freight, Ocean Freight, Rail Freight, and Express Delivery.

Utilize the tracking feature to monitor shipments.

Contact support through the provided channels for assistance.


Technologies Used

Frontend:

React.js

Redux

Axios

Material-UI


Backend:

Node.js

Express.js

MongoDB (Mongoose)

JWT for authentication


Other Tools:

Docker (for containerization)

Jest (for testing)

ESLint and Prettier (for code formatting and linting)



Contributing

Contributions are welcome! To contribute:

1. Fork the repository.


2. Create a new branch:

git checkout -b feature/YourFeatureName


3. Make your changes.


4. Commit your changes:

git commit -m 'Add some feature'


5. Push to the branch:

git push origin feature/YourFeatureName


6. Open a pull request.



Please ensure your code adheres to the project's coding standards and includes appropriate tests.

License

This project is licensed under the MIT License. See the LICENSE file for details.