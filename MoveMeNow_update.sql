USE MoveMeNow;

--user

CREATE TABLE Users (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(50) UNIQUE NOT NULL,
    Email VARCHAR(100) UNIQUE NOT NULL,
    Password VARCHAR(255) NOT NULL,
    Role ENUM('Customer', 'Admin', 'Driver', 'Mover') NOT NULL
);

--Customers
CREATE TABLE Customers (
                           CustomerID INT AUTO_INCREMENT PRIMARY KEY,
                           UserID INT NOT NULL,
                           Address VARCHAR(255),
                           FOREIGN KEY (UserID) REFERENCES Users(UserID)
);

--booking
CREATE TABLE Bookings (
                          BookingID INT AUTO_INCREMENT PRIMARY KEY,
                          CustomerID INT NOT NULL,
                          Date DATE NOT NULL,
                          PickupAddress VARCHAR(255) NOT NULL,
                          DeliveryAddress VARCHAR(255) NOT NULL,
                          Status ENUM('Scheduled', 'In Progress', 'Completed', 'Cancelled') DEFAULT 'Scheduled',
                          FOREIGN KEY (CustomerID) REFERENCES Customers(CustomerID)
);

--Vehicles
CREATE TABLE Vehicles (
                          VehicleID INT AUTO_INCREMENT PRIMARY KEY,
                          Type ENUM('Truck', 'Van') NOT NULL,
                          Availability BOOLEAN DEFAULT TRUE
);

--employees
CREATE TABLE Employees (
                           EmployeeID INT AUTO_INCREMENT PRIMARY KEY,
                           UserID INT NOT NULL,
                           AssignedVehicleID INT,
                           FOREIGN KEY (UserID) REFERENCES Users(UserID),
                           FOREIGN KEY (AssignedVehicleID) REFERENCES Vehicles(VehicleID)
);

--payments
CREATE TABLE Payments (
PaymentID INT AUTO_INCREMENT PRIMARY KEY,
    BookingID INT NOT NULL,
    Amount DECIMAL(10, 2) NOT NULL,
    Status ENUM('Pending', 'Completed', 'Failed') DEFAULT 'Pending',
    PaymentDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (BookingID) REFERENCES Bookings(BookingID)
);

--Inventory
CREATE TABLE Inventory (
                           InventoryID INT AUTO_INCREMENT PRIMARY KEY,
                           BookingID INT NOT NULL,
                           ItemDescription VARCHAR(255),
                           Weight DECIMAL(10, 2),
                           Quantity INT,
                           FOREIGN KEY (BookingID) REFERENCES Bookings(BookingID)
);

--notifications
CREATE TABLE Notifications (
                               NotificationID INT AUTO_INCREMENT PRIMARY KEY,
                               UserID INT NOT NULL,
                               Message TEXT NOT NULL,
                               SentAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                               FOREIGN KEY (UserID) REFERENCES Users(UserID)
);

--supportTickets
CREATE TABLE SupportTickets (
                                TicketID INT AUTO_INCREMENT PRIMARY KEY,
                                UserID INT NOT NULL,
                                Subject VARCHAR(255),
                                Description TEXT,
                                Status ENUM('Open', 'In Progress', 'Closed') DEFAULT 'Open',
                                CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                FOREIGN KEY (UserID) REFERENCES Users(UserID)
);

