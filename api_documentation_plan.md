### API Endpoint Documentation Plan for TenantController

This plan outlines the API endpoints derived from the `TenantController.php` and the functionalities depicted in the `account.png` image. It categorizes endpoints into Tenant Self-Service (user-facing) and Admin (restricted access) sections.

### General API Information:

*   **Base URL:** `/api`
*   **Authentication:** All endpoints require authentication.
    *   **Tenant Self-Service Endpoints:** Authenticated tenant user (e.g., via `auth:sanctum`).
    *   **Admin Endpoints:** Authenticated admin user with `admin` scope (e.g., `auth:sanctum`, `tokenCan('admin')`).
*   **Content-Type:** `application/json` for requests and responses (unless specified for file uploads).

---

### I. Tenant Self-Service API Endpoints (User-Facing)

These endpoints allow a logged-in tenant to manage their own account details, as depicted in the `account.png` UI.

#### 1. Get Tenant Profile Details

*   **HTTP Method:** `GET`
*   **Path:** `/api/v1/Account/profile`
*   **Description:** Retrieves the authenticated tenant's personal information, current unit, and profile photo path.
*   **Corresponds to Controller Method:** `TenantController::account()` (data retrieval part)
*   **Authentication:** `auth:sanctum`
*   **Responses:**
    *   `200 OK`:
        ```json
        {
            "id": 1,
            "first_name": "John",
            "last_name": "Doe",
            "email": "john.doe@example.com",
            "contact_num": "123-456-7890",
            "birth_date": "1990-01-01",
            "profile_photo_path": "uploads/tenants/tenant-1-123456789.jpg",
            "unit": {
                "id": 101,
                "unit_number": "Apt 101",
                "building_name": "Abcd House",
                "monthly_rent": 10000
                // ... other unit details
            }
        }
        ```
    *   `401 Unauthorized`: If no authenticated user.
    *   `404 Not Found`: If tenant record associated with the user is not found.

#### 2. Update Tenant Personal Information

*   **HTTP Method:** `PUT` (or `POST` for simpler forms)
*   **Path:** `/api/v1/Account/profile`
*   **Description:** Updates the authenticated tenant's personal details (first name, last name, contact number, birth date).
*   **Corresponds to Controller Method:** `TenantController::accountupdate()` (personal info update part)
*   **Authentication:** `auth:sanctum`
*   **Request Body:** `application/json`
    *   `first_name` (string, required): Tenant's first name. Max 255 chars.
    *   `last_name` (string, required): Tenant's last name. Max 255 chars.
    *   `contact_num` (string, optional): Tenant's contact number. Max 20 chars.
    *   `birth_date` (string, optional): Tenant's birth date (YYYY-MM-DD format).
*   **Responses:**
    *   `200 OK`:
        ```json
        {
            "message": "Account updated successfully."
        }
        ```
    *   `422 Unprocessable Entity`: For validation errors.
        ```json
        {
            "message": "The given data was invalid.",
            "errors": {
                "first_name": ["The first name field is required."]
            }
        }
        ```
    *   `401 Unauthorized`: If no authenticated user.
    *   `404 Not Found`: If tenant record associated with the user is not found.

#### 3. Update Tenant Profile Photo

*   **HTTP Method:** `POST`
*   **Path:** `/api/v1/Account/profile/avatar`
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
            "profile_photo_path": "uploads/tenants/tenant-1-new-timestamp.jpg"
        }
        ```
    *   `422 Unprocessable Entity`: For validation errors (e.g., invalid file type, size).
    *   `401 Unauthorized`: If no authenticated user.
    *   `404 Not Found`: If tenant record associated with the user is not found.

#### 4. Update Login Credentials (Email/Password)

*   **HTTP Method:** `POST` (or `PUT`)
*   **Path:** `/api/tenant/credentials`
*   **Description:** Updates the authenticated user's login email and/or password.
*   **Corresponds to Controller Method:** `TenantController::updatecredentials()`
*   **Authentication:** `auth:sanctum`
*   **Request Body:** `application/json`
    *   `email` (string, required): New email address. Must be unique and valid email format.
    *   `password` (string, optional): New password. Min 8 characters, confirmed. Max 72 chars.
    *   `password_confirmation` (string, required if `password` is present): Confirmation of the new password.
*   **Responses:**
    *   `200 OK`:
        ```json
        {
            "message": "Login credentials updated successfully."
        }
        ```
    *   `422 Unprocessable Entity`: For validation errors (e.g., email already exists, password mismatch).
    *   `401 Unauthorized`: If no authenticated user.

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
        *   `200 OK`: Returns an array of tenant objects.

*   **2. Show Specific Tenant Details**
    *   **HTTP Method:** `GET`
    *   **Path:** `/api/admin/tenants/{id}`
    *   **Description:** Retrieves detailed information for a specific tenant by ID.
    *   **Corresponds to Controller Method:** `TenantController::show()`
    *   **Authentication:** `auth:sanctum` with `admin` scope.
    *   **Responses:**
        *   `200 OK`: Returns a single tenant object.
        *   `404 Not Found`: If tenant ID does not exist.

*   **3. Update Specific Tenant Details (Admin)**
    *   **HTTP Method:** `PUT` (or `PATCH`)
    *   **Path:** `/api/admin/tenants/{id}`
    *   **Description:** Updates details for a specific tenant by ID.
    *   **Corresponds to Controller Method:** `TenantController::update()`
    *   **Authentication:** `auth:sanctum` with `admin` scope.
    *   **Request Body:** `application/json` with fields like `first_name`, `last_name`, `email`, `contact_num`, `unit_id`.
    *   **Responses:**
        *   `200 OK`:
            ```json
            {
                "message": "Tenant updated successfully",
                "tenant": { /* updated tenant object */ }
            }
            ```
        *   `422 Unprocessable Entity`: For validation errors.
        *   `404 Not Found`: If tenant ID does not exist.

*   **4. Archive a Tenant**
    *   **HTTP Method:** `DELETE`
    **Path:** `/api/admin/tenants/{id}/archive`
    *   **Description:** Soft-deletes a tenant, moving them to an archived state.
    *   **Corresponds to Controller Method:** `TenantController::archive()`
    *   **Authentication:** `auth:sanctum` with `admin` scope.
    *   **Responses:**
        *   `200 OK`:
            ```json
            {
                "message": "Tenant Archived Successfully",
                "data": { /* archived tenant object */ }
            }
            ```
        *   `404 Not Found`: If tenant ID does not exist.

*   **5. View Archived Tenants**
    *   **HTTP Method:** `GET`
    *   **Path:** `/api/admin/tenants/archive`
    *   **Description:** Retrieves a list of all soft-deleted (archived) tenants.
    *   **Corresponds to Controller Method:** `TenantController::viewAr chive()`
    *   **Authentication:** `auth:sanctum` with `admin` scope.
    *   **Responses:**
        *   `200 OK`: Returns an array of archived tenant objects.

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
                "tenant": { /* restored tenant object */ }
            }
            ```
        *   `404 Not Found`: If archived tenant ID does not exist.

---

### III. Other API Endpoints

*   **1. Autopay Setup**
    *   **HTTP Method:** `POST`
    *   **Path:** `/api/tenant/autopay-setup` (Assuming this is a tenant-initiated action)
    *   **Description:** Allows an authenticated tenant to set up their autopay payment method.
    *   **Corresponds to Controller Method:** `TenantController::autopaySetup()`
    *   **Authentication:** `auth:sanctum`
    *   **Request Body:** `application/json`
        *   `payment_method` (string, required): Details about the payment method (e.g., 'credit_card', 'bank_transfer', or a token from a payment gateway).
    *   **Responses:**
        *   `200 OK`:
            ```json
            {
                "message": "Autopay has been activated successfully!"
            }
            ```
        *   `422 Unprocessable Entity`: For validation errors.
        *   `401 Unauthorized`: If no authenticated user.