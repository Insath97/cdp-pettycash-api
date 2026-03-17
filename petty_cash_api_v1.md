# Petty Cash API Documentation (V1)

This document outlines the API endpoints, methods, and expected payloads for the Petty Cash management system.

---

## 1. Submit Petty Cash Request
**Endpoint:** `POST /api/v1/petty-cashes`  
**Description:** Submit a new petty cash request.  
**Permissions:** Publicly accessible (No specific permission required).

### Payload (multipart/form-data)
| Field | Type | Requirement | Description |
| :--- | :--- | :--- | :--- |
| `full_name` | String | Required | Requester's full name. |
| `email` | String | Required | Requester's email (Unique). |
| `branch_id` | Integer | Required | Foreign key to `branches` table. |
| `department_id` | Integer | Optional | Foreign key to `departments` table. |
| `date_needed` | Date | Required | Format: `YYYY-MM-DD`. |
| `category_id` | Integer | Required | Foreign key to `categories` table. |
| `type` | Enum | Required | `new_purchase`, `reimbursement`. |
| `amount` | Numeric | Required | Minimum: 0. |
| `description` | String | Optional | Notes about the request. |
| `account_number`| String | Required | Requester's bank account number. |
| `bank_name` | String | Required | Name of the bank. |
| `bank_branch` | String | Required | Branch name of the bank. |
| `receipt_image_path` | File | Required if `type=reimbursement` | Image/Receipt file (max 2MB). |

---

## 2. Verify Petty Cash
**Endpoint:** `PATCH /api/v1/petty-cashes/{id}/verify`  
**Description:** Transition a request from `pending` to `verified` or `rejected`.  
**Permissions:** `Petty Cash Verify`

### Payload (JSON)
| Field | Type | Requirement | Description |
| :--- | :--- | :--- | :--- |
| `status` | Enum | Required | `verified`, `rejected`. |
| `description` | String | Optional | Verification notes (saves to `verified_description` or `rejected_description`). |

---

## 3. Approve Petty Cash
**Endpoint:** `PATCH /api/v1/petty-cashes/{id}/approve`  
**Description:** Transition a request from `verified` to `approved` or `rejected`.  
**Permissions:** `Petty Cash Approve`

### Payload (JSON)
| Field | Type | Requirement | Description |
| :--- | :--- | :--- | :--- |
| `status` | Enum | Required | `approved`, `rejected`. |
| `description` | String | Optional | Approval notes (saves to `approved_description` or `rejected_description`). |

---

## 4. Update Payment Status
**Endpoint:** `PATCH /api/v1/petty-cashes/{id}/payment-status`  
**Description:** Update the payment stage of an `approved` request.  
**Permissions:** `Petty Cash Update Payment Status`

### Payload (JSON)
| Field | Type | Requirement | Description |
| :--- | :--- | :--- | :--- |
| `payment_status` | Enum | Required | `pending`, `onhold`, `paid`. |
| `description` | String | Optional | Payment notes (saves to `payment_description`). |

---

## 5. List Petty Cashes
**Endpoint:** `GET /api/v1/petty-cashes`  
**Permissions:** `Petty Cash Index`

### Query Parameters
- `search`: Search by reference number, full name, branch, or department.
- `status`: Filter by `pending`, `verified`, `approved`, `rejected`.
- `type`: Filter by `new_purchase`, `reimbursement`.
- `per_page`: Number of results (Default: 15).

---

## 6. Show Petty Cash
**Endpoint:** `GET /api/v1/petty-cashes/{id}`  
**Permissions:** `Petty Cash Index`
