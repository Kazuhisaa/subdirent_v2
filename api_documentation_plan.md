### API Endpoint Documentation Plan for TenantController

This plan outlines the API endpoints derived from the `TenantController.php` and the functionalities depicted in the tenant-facing UI images (`account.png`, `dashboard.png`, `maintenance.png`, `payments.png`, `property.png`). It categorizes endpoints into Tenant Self-Service (user-facing) and Admin (restricted access) sections.

### General API Information:

*   **Base URL:** `/api`
*   **Authentication:** All endpoints require authentication.
    *   **Tenant Self-Service Endpoints:** Authenticated tenant user (e.g., via `auth:sanctum`).
    *   **Admin Endpoints:** Authenticated admin user with `admin` scope (e.g., `auth:sanctum`, `tokenCan('admin')`).
*   **Content-Type:** `application/json` for requests and responses (unless specified for file uploads).

#### JSON Format Standards:

To maintain consistency and ease of integration, all API requests and responses will adhere to the following JSON formatting standards:

*   **Field Naming:** All field names in JSON request and response bodies should use `camelCase`.
*   **Date and Time Format:** All date and time values will be returned in ISO 8601 format (e.g., `YYYY-MM-DDTHH:mm:ssZ`).
*   **Success Response Structure:**
    *   Successful `GET` requests for single resources will return the resource object directly.
    *   Successful `GET` requests for collections will return an array of resource objects.
    *   Successful `POST`, `PUT`, `PATCH`, and `DELETE` requests will return a `message` key with a descriptive string, and optionally a `data` key containing the affected resource.
    ```json
    // Example success response for a POST/PUT operation
    {
        "message": "Resource created successfully.",
        "data": {
            "id": 1,
            "name": "New Resource"
        }
    }
    ```
*   **Error Response Structure:**
    *   All error responses will return a consistent JSON object containing `message`, `code`, and an optional `errors` object for validation failures.
    ```json
    // Example general error response
    {
        "message": "Something went wrong.",
        "code": "INTERNAL_SERVER_ERROR"
    }

    // Example validation error response (HTTP 422)
    {
        "message": "The given data was invalid.",
        "errors": {
            "fieldName": ["The fieldName field is required."],
            "anotherField": ["The anotherField may not be greater than X."]
        },
        "code": "VALIDATION_ERROR"
    }
    ```

---

### I. Tenant Self-Service API Endpoints (User-Facing)

These endpoints allow a logged-in tenant to manage their own account details and view relevant information across different sections of the application.

#### A. Account Endpoints (from `account.png`)

These endpoints facilitate the management of a tenant's personal profile and login credentials.

*   **1. Get Tenant Profile Details**
    *   **HTTP Method:** `GET`
    *   **Path:** `/api/tenant/profile`
    *   **Description:** Retrieves the authenticated tenant's personal information, current unit, and profile photo path.
    *   **Corresponds to Controller Method:** `TenantController::account()` (data retrieval part)
    *   **Authentication:** `auth:sanctum`
    *   **Responses:**
        *   `200 OK`:
            ```json
            {
                "id": 1,
                "firstName": "John",
                "lastName": "Doe",
                "email": "john.doe@example.com",
                "contactNum": "123-456-7890",
                "birthDate": "1990-01-01",
                "profilePhotoPath": "uploads/tenants/tenant-1-123456789.jpg",
                "unit": {
                    "id": 101,
                    "unitNumber": "Apt 101",
                    "buildingName": "Abcd House",
                    "monthlyRent": 10000
                    // ... other unit details (in camelCase)
                }
            }
            ```
        *   `401 Unauthorized`: (See Error Response Structure)
        *   `404 Not Found`: (See Error Response Structure)

*   **2. Update Tenant Personal Information**
    *   **HTTP Method:** `PUT` (or `POST` for simpler forms)
    *   **Path:** `/api/tenant/profile`
    *   **Description:** Updates the authenticated tenant's personal details (first name, last name, contact number, birth date).
    *   **Corresponds to Controller Method:** `TenantController::accountupdate()` (personal info update part)
    *   **Authentication:** `auth:sanctum`
    *   **Request Body:** `application/json`
        *   `firstName` (string, required): Tenant's first name. Max 255 chars.
        *   `lastName` (string, required): Tenant's last name. Max 255 chars.
        *   `contactNum` (string, optional): Tenant's contact number. Max 20 chars.
        *   `birthDate` (string, optional): Tenant's birth date (YYYY-MM-DD format).
    *   **Responses:**
        *   `200 OK`:
            ```json
            {
                "message": "Account updated successfully."
            }
            ```
        *   `422 Unprocessable Entity`: (See Error Response Structure for validation errors)
        *   `401 Unauthorized`: (See Error Response Structure)
        *   `404 Not Found`: (See Error Response Structure)

*   **3. Update Tenant Profile Photo**
    *   **HTTP Method:** `POST`
    *   **Path:** `/api/tenant/profile/avatar`
    *   **Description:** Uploads or updates the authenticated tenant's profile avatar.
    *   **Corresponds to Controller Method:** `TenantController::accountupdate()` (avatar upload part)
    *   **Authentication:** `auth:sanctum`
    *   **Request Body:** `multipart/form-data`
        *   `avatar` (file, optional): Image file (jpeg, png, jpg, webp). Max 2MB.
    *   **Responses:**
        *   `200 OK`:
            ```json
            {
                "message": "Profile photo updated successfully.",
                "profilePhotoPath": "uploads/tenants/tenant-1-new-timestamp.jpg"
            }
            ```
        *   `422 Unprocessable Entity`: (See Error Response Structure for validation errors)
        *   `401 Unauthorized`: (See Error Response Structure)
        *   `404 Not Found`: (See Error Response Structure)

*   **4. Update Login Credentials (Email/Password)**
    *   **HTTP Method:** `POST` (or `PUT`)
    *   **Path:** `/api/tenant/credentials`
    *   **Description:** Updates the authenticated user's login email and/or password.
    *   **Corresponds to Controller Method:** `TenantController::updatecredentials()`
    *   **Authentication:** `auth:sanctum`
    *   **Request Body:** `application/json`
        *   `email` (string, required): New email address. Must be unique and valid email format.
        *   `password` (string, optional): New password. Min 8 characters, confirmed. Max 72 chars.
        *   `passwordConfirmation` (string, required if `password` is present): Confirmation of the new password.
    *   **Responses:**
        *   `200 OK`:
            ```json
            {
                "message": "Login credentials updated successfully."
            }
            ```
        *   `422 Unprocessable Entity`: (See Error Response Structure for validation errors)
        *   `401 Unauthorized`: (See Error Response Structure)

#### B. Dashboard Endpoints (from `dashboard.png`)

These endpoints provide an overview of the tenant's key information, such as unit, payments, and maintenance.

*   **1. Get Tenant Dashboard Overview**
    *   **HTTP Method:** `GET`
    *   **Path:** `/api/tenant/dashboard`
    *   **Description:** Retrieves a comprehensive overview for the tenant's dashboard, including unit info, active contracts, recent payments, next unpaid due date, maintenance stats, and calendar events.
    *   **Corresponds to Controller Method:** `TenantController::home()` (data aggregation part)
    *   **Authentication:** `auth:sanctum`
    *   **Responses:**
        *   `200 OK`: Returns aggregated data including `tenant`, `activeContract`, `recentPayments`, `nextUnpaidDueDate`, `calendarEvents` (merged maintenance and payment events), `maintenanceCounts`, and `maintenanceRequests`. (Fields in `camelCase`)
        *   `401 Unauthorized`: (See Error Response Structure)
        *   `404 Not Found`: (See Error Response Structure)

#### C. Maintenance Endpoints (from `maintenance.png`)

These endpoints are for managing a tenant's maintenance requests.

*   **1. Get Tenant Maintenance Requests**
    *   **HTTP Method:** `GET`
    *   **Path:** `/api/tenant/maintenance/requests`
    *   **Description:** Retrieves a list of the authenticated tenant's maintenance requests, including status and recent ones.
    *   **Corresponds to Controller Method:** `TenantController::home()` (maintenance requests part). A dedicated method for listing all requests might be added if the current implementation only fetches a limited number for the dashboard.
    *   **Authentication:** `auth:sanctum`
    *   **Responses:**
        *   `200 OK`: Returns an array of maintenance request objects. (Fields in `camelCase`)
        *   `401 Unauthorized`: (See Error Response Structure)
        *   `404 Not Found`: (See Error Response Structure)

*   **2. Submit New Maintenance Request**
    *   **HTTP Method:** `POST`
    *   **Path:** `/api/tenant/maintenance/requests`
    *   **Description:** Allows the authenticated tenant to submit a new maintenance request. This functionality is implied by the UI.
    *   **Corresponds to:** *Requires a new API method in `TenantController` (e.g., `storeMaintenanceRequest`).*
    *   **Authentication:** `auth:sanctum`
    *   **Request Body:** `application/json`
        *   `category` (string, required): Category of the maintenance request.
        *   `description` (string, optional): Detailed description of the issue.
        *   `urgency` (string, required): Urgency level (e.g., 'Low', 'Medium', 'High').
        *   `attachments` (array of files, optional): Images related to the request (if file upload is supported).
    *   **Responses:**
        *   `201 Created`:
            ```json
            {
                "message": "Maintenance request submitted successfully.",
                "data": { /* newly created maintenance request object in camelCase */ }
            }
            ```
        *   `422 Unprocessable Entity`: (See Error Response Structure for validation errors)
        *   `401 Unauthorized`: (See Error Response Structure)

#### D. Payments Endpoints (from `payments.png`)

These endpoints provide detailed information about a tenant's payments and ledger.

*   **1. Get Tenant Payment Details**
    *   **HTTP Method:** `GET`
    *   **Path:** `/api/tenant/payments`
    *   **Description:** Retrieves a detailed overview of all payments for the authenticated tenant's active contract, including outstanding balance, penalties, and payment statuses.
    *   **Corresponds to Controller Method:** `TenantController::payments()` (data aggregation part)
    *   **Authentication:** `auth:sanctum`
    *   **Responses:**
        *   `200 OK`: Returns payment details including `tenant`, `activeContract`, `payments`, `nextMonth`, `outstanding`, `penalty`, `amountToPay`, and `paymentStatus`. (Fields in `camelCase`)
        *   `401 Unauthorized`: (See Error Response Structure)
        *   `404 Not Found`: (See Error Response Structure)

#### E. Property Endpoints (from `property.png`)

These endpoints provide information about the tenant's rented unit.

*   **1. Get Tenant Property Details**
    *   **HTTP Method:** `GET`
    *   **Path:** `/api/tenant/property`
    *   **Description:** Retrieves information about the authenticated tenant's unit and active contract, including external prediction data if available.
    *   **Corresponds to Controller Method:** `TenantController::property()` (data aggregation part)
    *   **Authentication:** `auth:sanctum`
    *   **Responses:**
        *   `200 OK`: Returns details including `tenant`, `contract`, `unit`, and `predictions`. (Fields in `camelCase`)
        *   `401 Unauthorized`: (See Error Response Structure)
        *   `404 Not Found`: (See Error Response Structure)

---

### II. Admin API Endpoints (Sanctum Protected)

These endpoints are already present in the `TenantController` and are protected by admin Sanctum tokens.

*   **1. List All Tenants**
    *   **HTTP Method:** `GET`
    *   **Path:** `/api/admin/tenants`
    *   **Description:** Retrieves a list of all active tenants, optionally with their associated unit information.
    *   **Corresponds to Controller Method:** `TenantController::index()`
    *   **Authentication:** `auth:sanctum` with `admin` scope.
    *   **Responses:**
        *   `200 OK`: Returns an array of tenant objects. (Fields in `camelCase`)

*   **2. Show Specific Tenant Details**
    *   **HTTP Method:** `GET`
    *   **Path:** `/api/admin/tenants/{id}`
    *   **Description:** Retrieves detailed information for a specific tenant by ID.
    *   **Corresponds to Controller Method:** `TenantController::show()`
    *   **Authentication:** `auth:sanctum` with `admin` scope.
    *   **Responses:**
        *   `200 OK`: Returns a single tenant object. (Fields in `camelCase`)
        *   `404 Not Found`: (See Error Response Structure)

*   **3. Update Specific Tenant Details (Admin)**
    *   **HTTP Method:** `PUT` (or `PATCH`)
    *   **Path:** `/api/admin/tenants/{id}`
    *   **Description:** Updates details for a specific tenant by ID.
    *   **Corresponds to Controller Method:** `TenantController::update()`
    *   **Authentication:** `auth:sanctum` with `admin` scope.
    *   **Request Body:** `application/json` with fields like `firstName`, `lastName`, `email`, `contactNum`, `unitId`.
    *   **Responses:**
        *   `200 OK`:
            ```json
            {
                "message": "Tenant updated successfully",
                "data": { /* updated tenant object in camelCase */ }
            }
            ```
        *   `422 Unprocessable Entity`: (See Error Response Structure for validation errors)
        *   `404 Not Found`: (See Error Response Structure)

*   **4. Archive a Tenant**
    *   **HTTP Method:** `DELETE`
    *   **Path:** `/api/admin/tenants/{id}/archive`
    *   **Description:** Soft-deletes a tenant, moving them to an archived state.
    *   **Corresponds to Controller Method:** `TenantController::archive()`
    *   **Authentication:** `auth:sanctum` with `admin` scope.
    *   **Responses:**
        *   `200 OK`:
            ```json
            {
                "message": "Tenant Archived Successfully",
                "data": { /* archived tenant object in camelCase */ }
            }
            ```
        *   `404 Not Found`: (See Error Response Structure)

*   **5. View Archived Tenants**
    *   **HTTP Method:** `GET`
    *   **Path:** `/api/admin/tenants/archive`
    *   **Description:** Retrieves a list of all soft-deleted (archived) tenants.
    *   **Corresponds to Controller Method:** `TenantController::viewArchive()`
    *   **Authentication:** `auth:sanctum` with `admin` scope.
    *   **Responses:**
        *   `200 OK`: Returns an array of archived tenant objects. (Fields in `camelCase`)

*   **6. Restore a Tenant**
    *   **HTTP Method:** `POST`
    *   **Path:** `/api/admin/tenants/{id}/restore`
    *   **Description:** Restores a soft-deleted (archived) tenant, making them active again.
    *   **Corresponds to Controller Method:** `TenantController::restore()`
    *   **Authentication:** `auth:sanctum` with `admin` scope.
    *   **Responses:**
        *   `200 OK`:
            ```json
            {
                "message": "Tenant restored successfully",
                "data": { /* restored tenant object in camelCase */ }
            }
            ```
        *   `404 Not Found`: (See Error Response Structure)

---

### III. Other API Endpoints

*   **1. Autopay Setup**
    *   **HTTP Method:** `POST`
    *   **Path:** `/api/tenant/autopay-setup` (Assuming this is a tenant-initiated action)
    *   **Description:** Allows an authenticated tenant to set up their autopay payment method.
    *   **Corresponds to Controller Method:** `TenantController::autopaySetup()`
    *   **Authentication:** `auth:sanctum`
    *   **Request Body:** `application/json`
        *   `paymentMethod` (string, required): Details about the payment method (e.g., 'credit_card', 'bank_transfer', or a token from a payment gateway).
    *   **Responses:**
        *   `200 OK`:
            ```json
            {
                "message": "Autopay has been activated successfully!"
            }
            ```
        *   `422 Unprocessable Entity`: (See Error Response Structure for validation errors)
        *   `401 Unauthorized`: (See Error Response Structure)
