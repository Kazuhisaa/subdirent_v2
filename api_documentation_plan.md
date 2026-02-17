### API Endpoint Documentation Plan for Subdirent

This document outlines the API endpoints for the Subdirent application, grouped by the core features depicted in the UI mockups.

### General API Information

*   **Base URL:** `/api`
*   **Authentication:** All tenant-facing and admin endpoints require authentication via Laravel Sanctum. Admin routes require `admin` scope.
*   **Content-Type:** `application/json`
*   **Standard JSON Response Format:**
    *   **Success Response (200 OK):**
        ```json
        {
            "message": "Operation successful.",
            "data": {
                // ... requested data payload ...
            }
        }
        ```
        *Note: Some endpoints, particularly from the mobile API, might include an additional `"success": true` field.*
    *   **Validation Error Response (422 Unprocessable Entity):**
        ```json
        {
            "message": "The given data was invalid.",
            "errors": {
                "field_name": ["The field name field is required."],
                "another_field": ["The another field must be a valid email."]
            }
        }
        ```
    *   **Error Response (e.g., 401 Unauthorized, 403 Forbidden, 404 Not Found, 500 Internal Server Error):**
        ```json
        {
            "message": "Error description."
        }
        ```
        *Note: Specific error messages and structures may vary slightly depending on the underlying exception.*

---

## Part I: Tenant-Facing API Endpoints (by Feature)

These endpoints correspond to the features available to a logged-in tenant.

### 1. Account Management (as seen in `account.png`)

These endpoints manage the tenant's personal profile and login credentials.

*   **Get Tenant Profile & Unit (Mobile)**
    *   **Description:** Retrieves the authenticated tenant's personal information, profile photo, and associated unit details for the mobile app.
    *   **Endpoint:** `GET /api/mobile/v1/Account/unit`
    *   **Corresponds to:** `Mobile\v1\AccountController::show()`
    *   **Example Response (200 OK):**
        ```json
        {
            "success": true,
            "message": "User profile and unit data retrieved successfully.",
            "data": {
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
        }
        ```
    *   **Example Error Response (404 Not Found):**
        ```json
        {
            "success": false,
            "message": "Tenant profile not found for the authenticated user."
        }
        ```
    *   **Other Error Responses:** `401 Unauthorized`

*   **Update Personal Information**
    *   **Description:** Updates the tenant's name, contact number, and birth date.
    *   **Endpoint:** `POST /api/account/profile` (or `PUT`)
    *   **Corresponds to:** `TenantController::accountupdate()`
    *   **Example Request:**
        ```json
        {
            "first_name": "Jonathan",
            "last_name": "Smith",
            "contact_num": "09171234567",
            "birth_date": "1992-05-15"
        }
        ```
    *   **Example Response (200 OK):**
        ```json
        {
            "message": "Account updated successfully."
        }
        ```
        *   **Error Responses:** `401 Unauthorized`, `422 Unprocessable Entity` (validation errors), `404 Not Found`

*   **Update Profile Photo**
    *   **Description:** Uploads a new profile photo for the tenant.
    *   **Endpoint:** `POST /api/account/profile/avatar`
    *   **Corresponds to:** File handling logic within `TenantController::accountupdate()`
    *   **Request Body:** `multipart/form-data` with an `avatar` file field (e.g., `avatar: <image_file>`).
    *   **Example Response (200 OK):**
        ```json
        {
            "message": "Profile photo updated successfully.",
            "profile_photo_path": "uploads/tenants/tenant-1-new-timestamp.jpg"
        }
        ```
        *   **Error Responses:** `401 Unauthorized`, `422 Unprocessable Entity` (file validation), `404 Not Found`

*   **Update Login Credentials**
    *   **Description:** Updates the tenant's login email and/or password.
    *   **Endpoint:** `POST /api/tenant/credentials`
    *   **Corresponds to:** `TenantController::updatecredentials()`
    *   **Example Request:**
        ```json
        { 
            "prev_password" : "prev_pass",
            "new_password": "new_secure_password",
            "password_confirmation": "new_secure_password"
        }
        ```
    *   **Example Response (200 OK):**
        ```json
        {
            "message": "Login credentials updated successfully."
        }
        ```
        *   **Error Responses:** `401 Unauthorized`, `422 Unprocessable Entity` (validation errors like email taken, password mismatch)

### 2. Dashboard (as seen in `dashboard.png`)

This endpoint provides a summary of all essential information for the tenant's dashboard.

*   **Get Dashboard Data**
    *   **Description:** Retrieves a consolidated object containing recent payments, maintenance request statuses, and payment schedule events for the calendar.
    *   **Conceptual Endpoint:** `GET /api/dashboard`
    *   **Corresponds to:** Data aggregation logic within `TenantController::home()`
    *   **Example Response (200 OK):**
        ```json
        {
            "message": "Dashboard data retrieved successfully.",
            "data": {
                "tenant": { /* tenant details */ },
                "active_contract": { /* contract details */ },
                "recent_payments": [ /* array of recent payments */ ],
                "next_unpaid_due_date": "Jan 15, 2025",
                "calendar_events": [ /* array of payment and maintenance events */ ],
                "maintenance_counts": { "pending": 2, "inprogress": 1, "completed": 5 },
                "maintenance_requests": [ /* array of recent requests */ ]
            }
        }
        ```
        *   **Error Responses:** `401 Unauthorized`, `404 Not Found` (if no active contract)

### 3. Payments & Ledger (as seen in `payments.png`)

Endpoints for handling all payment-related activities.

*   **Initiate Payment Session**
    *   **Description:** Creates a checkout session with PayMongo and provides a redirect URL for the user to complete the payment.
    *   **Endpoint:** `POST /api/tenants/{tenantId}/payments`
    *   **Corresponds to:** `PaymentController::createPayment()`
    *   **Example Request:**
        ```json
        {
            "amount": 15000,
            "for_month": "2025-02-01"
        }
        ```
    *   **Example Response (302 Redirect):** (The API will return a redirect to PayMongo's checkout page.)
        ```
        HTTP/1.1 302 Found
        Location: https://api.paymongo.com/v1/checkout_sessions/cs_xxxxxxx
        ```
        *   **Error Responses:** `401 Unauthorized`, `404 Not Found`, `422 Unprocessable Entity` (validation), `500 Internal Server Error` (PayMongo failure)

*   **Get Full Payment Ledger**
    *   **Description:** Retrieves a complete history of all payments made by the tenant.
    *   **Endpoint:** `GET /api/tenants/{tenantId}/ledger`
    *   **Corresponds to:** `TenantController::ledger()`
    *   **Example Response (200 OK):**
        ```json
        {
            "message": "Payment ledger retrieved successfully.",
            "data": [
                {
                    "id": 1,
                    "amount": 10000,
                    "payment_date": "2024-01-15",
                    "for_month": "2024-01-01",
                    "remarks": "Rent Payment for January 2024",
                    "invoice_no": "INV-12345"
                },
                {
                    "id": 2,
                    "amount": 10000,
                    "payment_date": "2024-02-14",
                    "for_month": "2024-02-01",
                    "remarks": "Rent Payment for February 2024",
                    "invoice_no": "INV-12346"
                }
            ]
        }
        ```
        *   **Error Responses:** `401 Unauthorized`, `404 Not Found`

*   **Download an Invoice**
    *   **Description:** Downloads a specific invoice as a PDF file.
    *   **Endpoint:** `GET /api/payments/{payment}/invoice/download`
    *   **Corresponds to:** `PaymentController::downloadInvoice()`
    *   **Example Response (200 OK):** (Returns a PDF file directly)
        ```
        HTTP/1.1 200 OK
        Content-Type: application/pdf
        Content-Disposition: attachment; filename="Invoice-INV-12345.pdf"
        ```
        *   **Error Responses:** `401 Unauthorized`, `404 Not Found`

*   **Setup Autopay**
    *   **Description:** Configures or updates the tenant's automatic payment settings.
    *   **Endpoint:** `POST /api/tenant/autopay-setup`
    *   **Corresponds to:** `TenantController::autopaySetup()`
    *   **Example Request:**
        ```json
        {
            "payment_method": "stripe_card_token_xxxx"
        }
        ```
    *   **Example Response (200 OK):**
        ```json
        {
            "message": "Autopay has been activated successfully!"
        }
        ```
        *   **Error Responses:** `401 Unauthorized`, `422 Unprocessable Entity`

### 4. Property Details (as seen in `property.png`)

*   **Get Property & Unit Details**
    *   **Description:** Retrieves detailed information about the tenant's current unit, including contract details.
    *   **Conceptual Endpoint:** `GET /api/property`
    *   **Corresponds to:** Data retrieval logic within `TenantController::property()`
    *   **Example Response (200 OK):**
        ```json
        {
            "message": "Property details retrieved successfully.",
            "data": {
                "tenant": { /* tenant details */ },
                "contract": { /* active contract details */ },
                "unit": { /* unit details */ },
                "predictions": [ /* array of property value predictions */ ]
            }
        }
        ```
        *   **Error Responses:** `401 Unauthorized`, `404 Not Found`

### 5. Maintenance (as seen in `maintenance.png`)

*   **List Maintenance Requests**
    *   **Description:** Retrieves a list of the tenant's recent maintenance requests.
    *   **Note:** This data is currently fetched as part of the `GET /api/dashboard` endpoint (`TenantController::home()`).
    *   **Conceptual Endpoint for dedicated list:** `GET /api/maintenance/requests` (if implemented separately)
    *   **Example Response (part of dashboard data):**
        ```json
        {
            // ... part of dashboard data ...
            "maintenance_requests": [
                { "id": 1, "category": "Plumbing", "status": "In Progress", "description": "Leaky faucet" },
                { "id": 2, "category": "Electrical", "status": "Pending", "description": "Outlet not working" }
            ]
        }
        ```

*   **Submit Maintenance Request**
    *   **Description:** Allows a tenant to submit a new maintenance request.
    *   **Note:** No dedicated API endpoint for this action was found in `TenantController` or `PaymentController`. This would typically be a `POST /api/maintenance/requests` endpoint.

---

## Part II: Admin API Endpoints

These endpoints are for administrative use and require admin-level permissions.

*   **List Tenants**
    *   **Endpoint:** `GET /api/admin/tenants`
    *   **Corresponds to:** `TenantController::index()`
    *   **Example Response (200 OK):**
        ```json
        {
            "message": "Tenants retrieved successfully.",
            "data": [
                { "id": 1, "first_name": "John", "last_name": "Doe", "unit": { /* unit details */ } },
                { "id": 2, "first_name": "Jane", "last_name": "Smith", "unit": { /* unit details */ } }
            ]
        }
        ```
        *   **Error Responses:** `401 Unauthorized`, `403 Forbidden`

*   **Show Specific Tenant**
    *   **Endpoint:** `GET /api/admin/tenants/{id}`
    *   **Corresponds to:** `TenantController::show()`
    *   **Example Response (200 OK):**
        ```json
        {
            "message": "Tenant retrieved successfully.",
            "data": { "id": 1, "first_name": "John", "last_name": "Doe", "unit": { /* unit details */ } }
        }
        ```
        *   **Error Responses:** `401 Unauthorized`, `403 Forbidden`, `404 Not Found`

*   **Update Specific Tenant**
    *   **Endpoint:** `PUT /api/admin/tenants/{id}`
    *   **Corresponds to:** `TenantController::update()`
    *   **Example Request:**
        ```json
        {
            "first_name": "Jonathan",
            "email": "jonathan.doe@example.com",
            "unit_id": 102
        }
        ```
    *   **Example Response (200 OK):**
        ```json
        {
            "message": "Tenant updated successfully",
            "tenant": { /* updated tenant object */ }
        }
        ```
        *   **Error Responses:** `401 Unauthorized`, `403 Forbidden`, `404 Not Found`, `422 Unprocessable Entity`

*   **Archive Tenant**
    *   **Endpoint:** `DELETE /api/admin/tenants/{id}/archive`
    *   **Corresponds to:** `TenantController::archive()`
    *   **Example Response (200 OK):**
        ```json
        {
            "message": "Tenant Archived Successfully",
            "data": { /* archived tenant object */ }
        }
        ```
        *   **Error Responses:** `401 Unauthorized`, `403 Forbidden`, `404 Not Found`

*   **View Archived Tenants**
    *   **Endpoint:** `GET /api/admin/tenants/archive`
    *   **Corresponds to:** `TenantController::viewArchive()`
    *   **Example Response (200 OK):**
        ```json
        {
            "message": "Archived tenants retrieved successfully.",
            "data": [
                { "id": 3, "first_name": "Archived", "last_name": "Tenant", "unit": { /* unit details */ } }
            ]
        }
        ```
        *   **Error Responses:** `401 Unauthorized`, `403 Forbidden`

*   **Restore Tenant**
    *   **Endpoint:** `POST /api/admin/tenants/{id}/restore`
    *   **Corresponds to:** `TenantController::restore()`
    *   **Example Response (200 OK):**
        ```json
        {
            "message": "Tenant restored successfully",
            "tenant": { /* restored tenant object */ }
        }
        ```
        *   **Error Responses:** `401 Unauthorized`, `403 Forbidden`, `404 Not Found`

*   **List All Payments (Admin)**
    *   **Endpoint:** `GET /api/admin/payments`
    *   **Corresponds to:** `PaymentController::showIndex()`
    *   **Example Response (200 OK):**
        ```json
        {
            "message": "Payments retrieved successfully.",
            "data": [
                { "id": 1, "amount": 10000, "tenant": { /* tenant details */ } },
                { "id": 2, "amount": 5000, "tenant": { /* tenant details */ } }
            ]
        }
        ```
        *   **Error Responses:** `401 Unauthorized`, `403 Forbidden`

*   **Archive Payment (Admin)**
    *   **Endpoint:** `DELETE /api/admin/payments/{id}`
    *   **Corresponds to:** `PaymentController::archive()`
    *   **Example Response (200 OK):**
        ```json
        {
            "message": "Payment archived successfully."
        }
        ```
        *   **Error Responses:** `401 Unauthorized`, `403 Forbidden`, `404 Not Found`

*   **View Archived Payments (Admin)**
    *   **Endpoint:** `GET /api/admin/payments/archive`
    *   **Corresponds to:** `PaymentController::viewArchive()`
    *   **Example Response (200 OK):**
        ```json
        {
            "message": "Archived payments retrieved successfully.",
            "data": [
                { "id": 3, "amount": 7500, "tenant": { /* tenant details */ } }
            ]
        }
        ```
        *   **Error Responses:** `401 Unauthorized`, `403 Forbidden`

*   **Restore Payment (Admin)**
    *   **Endpoint:** `POST /api/admin/payments/{id}/restore`
    *   **Corresponds to:** `PaymentController::restore()`
    *   **Example Response (200 OK):**
        ```json
        {
            "message": "Payment restored successfully."
        }
        ```
        *   **Error Responses:** `401 Unauthorized`, `403 Forbidden`, `404 Not Found`

---

## Part III: Webhook Endpoints

*   **PayMongo Webhook Handler**
    *   **Description:** Receives and processes incoming payment events from PayMongo.
    *   **Endpoint:** `POST /api/webhook/paymongo`
    *   **Corresponds to:** `PaymentController::handleWebhook()`
    *   **Authentication:** None (Public). Signature validation should be handled within the method if required by PayMongo.
    *   **Example Request:** (Payload from PayMongo)
        ```json
        {
            "data": {
                "id": "event_xxxxxxxxxxxxxxxxxxxx",
                "type": "event",
                "attributes": {
                    "livemode": false,
                    "type": "checkout_session.payment.paid",
                    "data": {
                        "id": "cs_xxxxxxxxxxxxxxxxxxxx",
                        "type": "checkout_session",
                        "attributes": {
                            "billing": { "email": "john.doe@example.com" },
                            "metadata": { "tenant_id": 1, "for_month": "2025-02-01" },
                            "payments": [
                                {
                                    "attributes": {
                                        "amount": 1500000,
                                        "currency": "PHP",
                                        "reference_number": "PM-REF-12345",
                                        "description": "Tenant Monthly Payment"
                                    }
                                }
                            ]
                        }
                    }
                }
            }
        }
        ```
    *   **Example Success Response (200 OK):**
        ```json
        {
            "status": "ok",
            "payment_status": "paid"
        }
        ```
    *   **Example Ignored Response (200 OK):**
        ```json
        {
            "status": "ignored"
        }
        ```
    *   **Example Error Response (500 Internal Server Error):**
        ```json
        {
            "status": "error",
            "message": "Invoice PDF generation failed: ..."
        }
        ```
