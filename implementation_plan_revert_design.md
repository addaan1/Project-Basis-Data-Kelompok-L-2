# Implementation Plan - Revert Dashboard Design

The user requests to restore the "Original" dashboard design, characterized by prominent Orange headers and Green body cards.

## Proposed Changes

### Dashboard View
#### [MODIFY] [dashboard.blade.php](file:///c:/xampp/htdocs/WarungPadi2/WarungPadi/resources/views/dashboard.blade.php)
-   **Structure**: 
    -   Row 1: "Tren Pendapatan" (Left, 8 cols) | "Volume Panen Terjual" (Right, 4 cols).
    -   Row 2: "Aktivitas Terakhir" (Left, 8 cols) | "Status Negosiasi" (Right, 4 cols).
-   **Styling**:
    -   Headers: Orange (`bg-warning`, text-white).
    -   Bodies: Green (`bg-success`, text-white).
    -   Charts: Adjusted colors to contrast well with green background (White lines/text).

## Verification Plan

### Manual Verification
-   **Visual Check**: Open Dashboard. Verify:
    -   Headers are Orange.
    -   Backgrounds are Green.
    -   Charts are visible and use the full 12-month data.
    -   Layout matches the reference image (Screen Shot 1).
