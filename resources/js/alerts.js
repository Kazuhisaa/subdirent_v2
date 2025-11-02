// public/js/alerts.js

// 🌈 Helper to read CSS variables dynamically
function cssVar(name) {
    return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
}

// 🏙️ Brand palette from your :root
const COLOR_PRIMARY_DARK = cssVar("--blue-900");
const COLOR_PRIMARY = cssVar("--blue-600");
const COLOR_PRIMARY_LIGHT = cssVar("--blue-400");
const COLOR_SUCCESS = "#28a745";
const COLOR_WARNING = "#ffc107";
const COLOR_ERROR = "#dc3545";
const COLOR_INFO = "#17a2b8";

// 💫 Base SweetAlert2 styling
const swalBaseConfig = {
    background: "#ffffff",
    color: COLOR_PRIMARY_DARK,
    confirmButtonColor: COLOR_PRIMARY,
    customClass: {
        popup: "rounded-4 shadow-lg border-0",
        title: "fw-semibold text-blue-800",
        confirmButton: "rounded-pill px-4 py-2 fw-semibold",
        cancelButton: "rounded-pill px-4 py-2 fw-semibold",
    },
    buttonsStyling: false,
    showClass: {
        popup: "animate__animated animate__fadeInDown",
    },
    hideClass: {
        popup: "animate__animated animate__fadeOutUp",
    },
};

// ✅ Success Alert
function showSuccess(message, title = "Success!") {
    Swal.fire({
        ...swalBaseConfig,
        icon: "success",
        title,
        text: message,
        confirmButtonColor: COLOR_PRIMARY,
        iconColor: COLOR_PRIMARY_LIGHT,
    });
}

// ❌ Error Alert
function showError(message, title = "Error!") {
    Swal.fire({
        ...swalBaseConfig,
        icon: "error",
        title,
        text: message,
        confirmButtonColor: COLOR_ERROR,
        iconColor: COLOR_ERROR,
    });
}

// ⚠️ Warning Alert
function showWarning(message, title = "Warning!") {
    Swal.fire({
        ...swalBaseConfig,
        icon: "warning",
        title,
        text: message,
        confirmButtonColor: COLOR_WARNING,
        iconColor: COLOR_WARNING,
    });
}

// ℹ️ Info Alert
function showInfo(message, title = "Notice") {
    Swal.fire({
        ...swalBaseConfig,
        icon: "info",
        title,
        text: message,
        confirmButtonColor: COLOR_INFO,
        iconColor: COLOR_INFO,
    });
}

// ❓ Confirmation Dialog
function confirmAction(
    message = "Are you sure?",
    confirmText = "Yes, continue",
    cancelText = "Cancel",
    onConfirm = null
) {
    Swal.fire({
        ...swalBaseConfig,
        title: "Confirm Action",
        text: message,
        icon: "question",
        iconColor: COLOR_PRIMARY_LIGHT,
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        confirmButtonColor: COLOR_PRIMARY,
        cancelButtonColor: COLOR_PRIMARY_DARK,
    }).then((result) => {
        if (result.isConfirmed && typeof onConfirm === "function") {
            onConfirm();
        }
    });
}

// 🌍 Expose to global scope
window.showSuccess = showSuccess;
window.showError = showError;
window.showWarning = showWarning;
window.showInfo = showInfo;
window.confirmAction = confirmAction;
