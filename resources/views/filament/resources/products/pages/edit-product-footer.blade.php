<style>
    .fi-form-actions {
        position: sticky !important;
        bottom: 0px !important;
        z-index: 30 !important;
        background-color: rgb(255 255 255 / 80%) !important;
        backdrop-filter: blur(8px) !important;
        padding: 1rem !important;
        border-top: 1px solid #e5e7eb !important;
        margin-left: -1.5rem !important;
        margin-right: -1.5rem !important;
        box-shadow: 0 -4px 6px -1px rgb(0 0 0 / 0.05) !important;
        transition: all 0.2s !important;
    }
    .dark .fi-form-actions {
        background-color: rgb(17 24 39 / 80%) !important;
        border-top-color: #1f2937 !important;
        box-shadow: 0 -4px 6px -1px rgb(0 0 0 / 0.2) !important;
    }
    .fi-form-actions button[disabled],
    .fi-form-actions button:disabled {
        background-color: #e5e7eb !important;
        color: #9ca3af !important;
        border-color: #e5e7eb !important;
        cursor: not-allowed !important;
        opacity: 0.65 !important;
        pointer-events: none !important;
        box-shadow: none !important;
    }
    .dark .fi-form-actions button[disabled],
    .dark .fi-form-actions button:disabled {
        background-color: #374151 !important;
        color: #4b5563 !important;
        border-color: #1f2937 !important;
    }
    @media (min-width: 1024px) {
        .fi-form-actions {
            bottom: 1.5rem !important;
            border-radius: 0.75rem !important;
            border: 1px solid #e5e7eb !important;
            margin-left: 0px !important;
            margin-right: 0px !important;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1) !important;
        }
        .dark .fi-form-actions {
            border-color: #1f2937 !important;
        }
    }
</style>
