# Tenant Dashboard API Architecture (Modular)

This document outlines the design for a set of new, modular API endpoints to provide data for the tenant dashboard. The goal is to separate data-gathering logic into focused, reusable endpoints. This approach is more flexible and scalable than a single monolithic endpoint.

## 1. The Problem

The original `TenantController@home` function is a "fat controller," responsible for fetching all dashboard data at once. A single API endpoint for this would be similarly monolithic, forcing the client to load all data even if it only needs a piece of it.

## 2. Proposed Solution: Modular, Resource-Focused API Endpoints

We will break down the data requirements into logical resources and create a dedicated API endpoint for each. All endpoints are authenticated via Sanctum.

---

### API Endpoint 1: Active Contract

Returns the tenant's current active or ongoing contract.

- **URL:** `/api/mobile/tenant/contract`
- **Method:** `GET`
- **Controller:** `App\Http\Controllers\Api\TenantContractController@show`
- **Example JSON Response:**
    ```json
    {
      "data": {
        "id": 1,
        "contract_start": "2025-01-01",
        "contract_end": "2026-01-01",
        "monthly_payment": 15000,
        "status": "active"
      }
    }
    ```

---

### API Endpoint 2: Payments Summary

Returns a summary of recent payments and the next upcoming payment due date.

- **URL:** `/api/tenant/payments/summary`
- **Method:** `GET`
- **Controller:** `App\Http\Controllers\Api\TenantPaymentController@summary`
- **Example JSON Response:**
    ```json
    {
      "data": {
        "recentPayments": [
          {
            "id": 123,
            "amount": 15000,
            "payment_date": "2025-12-15",
            "for_month": "2025-12-01"
          }
        ],
        "nextUnpaidDueDate": "Jan 15, 2026"
      }
    }
    ```

---

### API Endpoint 3: Calendar Events

Returns all payment and maintenance events for the tenant's calendar.

- **URL:** `/api/tenant/calendar`
- **Method:** `GET`
- **Controller:** `App\Http\Controllers\Api\TenantCalendarController@index`
- **Example JSON Response:**
    ```json
    {
      "data": [
        {
          "title": "Due - ₱15,000.00",
          "start": "2026-01-15",
          "color": "#dc3545",
          "type": "payment"
        },
        {
          "title": "Service: Plumbing Repair",
          "start": "2026-01-20",
          "color": "#6f42c1",
          "type": "maintenance"
        }
      ]
    }
    ```

---

### API Endpoint 4: Maintenance Summary

Returns statistics on maintenance requests and a list of the most recent requests.

- **URL:** `/api/tenant/maintenance/summary`
- **Method:** `GET`
- **Controller:** `App\Http\Controllers\Api\TenantMaintenanceController@summary`
- **Example JSON Response:**
    ```json
    {
      "data": {
        "stats": {
          "pending": 1,
          "inprogress": 0,
          "completed": 2
        },
        "recentRequests": [
          {
            "id": 45,
            "category": "Plumbing",
            "status": "Pending",
            "created_at": "2026-01-09T10:00:00Z"
          }
        ]
      }
    }
    ```

## 3. Benefits of this Approach

- **Separation of Concerns**: Each endpoint has a single, clear responsibility.
- **Improved Frontend Performance**: The client can lazy-load different parts of the dashboard, leading to a faster initial page load.
- **Reusability**: These smaller, focused endpoints are much more likely to be reused by other clients (e.g., a mobile app).
- **Testability & Maintainability**: Smaller controllers are easier to test and maintain.
