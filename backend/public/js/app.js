document.querySelectorAll(".toggle-password")
    .forEach((button) => {
        button.addEventListener("click", () => {
            const targetId = button.dataset.target;
            const input = document.getElementById(targetId);

            if (!input) return;

            const isPassword = input.type === "password";

            input.type = isPassword ? "text" : "password";

            button.setAttribute(
                "aria-label",
                isPassword ? "Hide password" : "Show password"
            );

            button.innerHTML = isPassword
                ? `
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="m3 3 18 18" />
                                <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8" />
                                <path d="M9.9 4.2A10.7 10.7 0 0 1 12 4c6.5 0 10 8 10 8a18 18 0 0 1-3.2 4.3" />
                                <path d="M6.6 6.6C3.7 8.8 2 12 2 12s3.5 8 10 8a10.7 10.7 0 0 0 4.1-.8" />
                            </svg>
                        `
                : `
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        `;
        });
});

// Basic client-side password confirmation check
// const form = document.getElementById("reset-password-form");

// form.addEventListener("submit", (event) => {
//     const password = document.getElementById("password").value;
//     const confirmation = document.getElementById(
//         "password_confirmation"
//     ).value;
//
//     if (password !== confirmation) {
//         event.preventDefault();
//
//         alert("Passwords do not match.");
//     }
// });