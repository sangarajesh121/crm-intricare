/**
 * -------------------------------------------------------------
 * Global Utility Script
 * Location: /public/js/app.js
 * -------------------------------------------------------------
 * Contains:
 *  - Bootstrap Toast Notification (showToast)
 *  - jQuery Validation Setup (initFormValidation)
 * -------------------------------------------------------------
 */


$(document).ready(function () {


    /**
     *  Toast Notification
     * @param {'success' | 'error' | 'info'} type
     * @param {string} message
     */
    window.showToast = function (type, message) {
        const bgClass =
            type === "success" ? "bg-success" :
            type === "error" ? "bg-danger" :
            "bg-info";

        const toastHTML = `
            <div class="toast align-items-center text-white ${bgClass} border-0 mb-2"
                role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>`;

        const container = document.getElementById("toastContainer");
        if (!container) return console.error("Toast container not found.");

        container.insertAdjacentHTML("beforeend", toastHTML);
        const toastEl = container.lastElementChild;
        const bsToast = new bootstrap.Toast(toastEl);
        bsToast.show();

        // Remove toast from DOM after hidden
        toastEl.addEventListener("hidden.bs.toast", () => toastEl.remove());
    };


});
