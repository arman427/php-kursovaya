document.addEventListener("DOMContentLoaded", function () {
   function showStoreToast(message, type) {
      if (!message) return;
      let toast = document.querySelector(".store-toast");
      if (!toast) {
         toast = document.createElement("div");
         toast.className = "store-toast";
         toast.setAttribute("role", "status");
         document.body.appendChild(toast);
      }
      toast.textContent = message;
      toast.classList.toggle("store-toast--error", type === "error");
      toast.classList.add("is-visible");
      window.clearTimeout(toast.hideTimer);
      toast.hideTimer = window.setTimeout(function () {
         toast.classList.remove("is-visible");
      }, 3200);
   }

   function updateHeaderCounters(data) {
      const cartBadge = document.querySelector(".header-action--cart b");
      const favoriteLink = document.querySelector('.header-action[aria-label="Избранное"]');
      const favoriteBadge = favoriteLink ? favoriteLink.querySelector("b") : null;
      if (cartBadge && typeof data.cartCount !== "undefined") cartBadge.textContent = data.cartCount;
      if (favoriteLink && typeof data.favoriteCount !== "undefined") {
         if (data.favoriteCount > 0) {
            if (favoriteBadge) favoriteBadge.textContent = data.favoriteCount;
            else {
               const badge = document.createElement("b");
               badge.textContent = data.favoriteCount;
               favoriteLink.querySelector(".header-action__visual").appendChild(badge);
            }
         } else if (favoriteBadge) favoriteBadge.remove();
      }
   }

   document.addEventListener("submit", function (event) {
      const form = event.target.closest("form");
      if (!form || !form.querySelector('[name="store_action"]')) return;
      const action = form.querySelector('[name="store_action"]').value;
      if (!["add_cart", "toggle_favorite", "remove_favorite"].includes(action)) return;

      event.preventDefault();
      const submit = form.querySelector("button[type=submit]");
      if (submit) submit.disabled = true;
      fetch(form.action || window.location.href, {
         method: "POST",
         body: new FormData(form),
         headers: { "X-Requested-With": "XMLHttpRequest", "Accept": "application/json" }
      }).then(function (response) {
         return response.json();
      }).then(function (data) {
         updateHeaderCounters(data);
         showStoreToast(data.message, data.type);
         if (!data.ok) return;
         if (action === "remove_favorite") {
            const card = form.closest(".catalog-product-card");
            if (card) card.remove();
         }
         if (action === "toggle_favorite" || action === "remove_favorite") {
            const favoriteButton = form.querySelector(".favorite-button, .favorite-link");
            const isActive = action === "toggle_favorite" && data.message.indexOf("добавлен") !== -1;
            if (favoriteButton) {
               favoriteButton.classList.toggle("is-active", isActive);
               if (favoriteButton.classList.contains("favorite-link")) {
                  favoriteButton.lastChild.textContent = isActive ? " В избранном" : " Добавить в избранное";
               }
            }
         }
      }).catch(function () {
         showStoreToast("Не удалось выполнить действие. Проверьте соединение.", "error");
      }).finally(function () {
         if (submit) submit.disabled = false;
      });
   });

   document.querySelectorAll(".quantity-control").forEach(function (form) {
      const input = form.querySelector('input[name="quantity"]');
      if (!input) return;
      const stepper = document.createElement("span");
      stepper.className = "quantity-stepper";
      const decrease = document.createElement("button");
      decrease.type = "button";
      decrease.dataset.quantityChange = "-1";
      decrease.setAttribute("aria-label", "Уменьшить количество");
      decrease.textContent = "−";
      const increase = document.createElement("button");
      increase.type = "button";
      increase.dataset.quantityChange = "1";
      increase.setAttribute("aria-label", "Увеличить количество");
      increase.textContent = "+";
      input.parentNode.insertBefore(stepper, input);
      stepper.append(decrease, input, increase);
      form.querySelectorAll("[data-quantity-change]").forEach(function (button) {
         button.addEventListener("click", function () {
            const step = Number(button.dataset.quantityChange);
            const value = Math.max(Number(input.min) || 1, Math.min(Number(input.max) || 99, Number(input.value) + step));
            if (value !== Number(input.value)) {
               input.value = value;
               form.requestSubmit();
            }
         });
      });
   });

   const modal = document.querySelector("#auth-modal");
   const form = modal ? modal.querySelector(".auth-form") : null;
   const title = modal ? modal.querySelector("#auth-title") : null;
   const action = form ? form.querySelector('[name="auth_action"]') : null;
   const confirmation = form ? form.querySelector(".register-only") : null;

   function setAuthMode(mode) {
      const isRegister = mode === "register";
      if (title)
         title.textContent = isRegister ? "Регистрация" : "Войти в аккаунт";
      if (action) action.value = mode;
      if (confirmation) confirmation.classList.toggle("is-visible", isRegister);
      document.querySelectorAll("[data-auth-tab]").forEach(function (tab) {
         tab.classList.toggle("is-active", tab.dataset.authTab === mode);
      });
   }

   function openModal() {
      if (!modal) return;
      modal.classList.add("is-open");
      modal.setAttribute("aria-hidden", "false");
      document.body.classList.add("modal-open");
      const firstInput = modal.querySelector('input[type="email"]');
      if (firstInput) firstInput.focus();
   }

   function closeModal() {
      if (!modal) return;
      modal.classList.remove("is-open");
      modal.setAttribute("aria-hidden", "true");
      document.body.classList.remove("modal-open");
   }

   const mobileMenuToggle = document.querySelector("[data-mobile-menu-toggle]");
   const siteHeader = document.querySelector(".site-header");
   if (mobileMenuToggle && siteHeader) {
      mobileMenuToggle.addEventListener("click", function () {
         const isOpen = siteHeader.classList.toggle("menu-is-open");
         mobileMenuToggle.setAttribute("aria-expanded", String(isOpen));
      });
      document.querySelectorAll(".site-nav__link").forEach(function (link) {
         link.addEventListener("click", function () {
            siteHeader.classList.remove("menu-is-open");
            mobileMenuToggle.setAttribute("aria-expanded", "false");
         });
      });
   }

   const profileMenus = document.querySelectorAll("[data-profile-menu]");

   function setProfileMenuState(menu, isOpen) {
      const toggle = menu.querySelector("[data-profile-toggle]");
      const dropdown = menu.querySelector("[data-profile-dropdown]");
      menu.classList.toggle("is-open", isOpen);
      if (toggle) toggle.setAttribute("aria-expanded", String(isOpen));
      if (dropdown) dropdown.setAttribute("aria-hidden", String(!isOpen));
   }

   profileMenus.forEach(function (menu) {
      const toggle = menu.querySelector("[data-profile-toggle]");
      if (!toggle) return;

      toggle.addEventListener("click", function (event) {
         event.stopPropagation();
         const shouldOpen = !menu.classList.contains("is-open");
         profileMenus.forEach(function (otherMenu) {
            setProfileMenuState(otherMenu, false);
         });
         setProfileMenuState(menu, shouldOpen);
      });
   });

   document.addEventListener("click", function (event) {
      profileMenus.forEach(function (menu) {
         if (!menu.contains(event.target)) setProfileMenuState(menu, false);
      });
   });

   document.querySelectorAll("[data-auth-open]").forEach(function (button) {
      button.addEventListener("click", openModal);
   });
   document.querySelectorAll("[data-auth-close]").forEach(function (button) {
      button.addEventListener("click", closeModal);
   });
   document.querySelectorAll("[data-auth-tab]").forEach(function (tab) {
      tab.addEventListener("click", function () {
         setAuthMode(tab.dataset.authTab);
      });
   });

   const productsGrid = document.querySelector("[data-products-grid]");
   document.querySelectorAll("[data-view]").forEach(function (button) {
      button.addEventListener("click", function () {
         const view = button.dataset.view;
         document.querySelectorAll("[data-view]").forEach(function (otherButton) {
            otherButton.classList.toggle("is-active", otherButton === button);
         });
         if (productsGrid) productsGrid.classList.toggle("is-list", view === "list");
      });
   });

   document.addEventListener("keydown", function (event) {
      if (event.key !== "Escape") return;
      closeModal();
      profileMenus.forEach(function (menu) {
         setProfileMenuState(menu, false);
      });
   });

    if (document.querySelector(".form-error, .auth-notice")) openModal();
});
