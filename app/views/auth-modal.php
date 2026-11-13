<div class="modal" id="auth-modal" aria-hidden="true">
    <div class="modal__backdrop" data-auth-close></div>
    <div class="modal__dialog" role="dialog" aria-modal="true" aria-labelledby="auth-title">
        <button class="modal__close" type="button" data-auth-close aria-label="Закрыть">×</button>
        <h2 id="auth-title">Войти в аккаунт</h2>
        <?php if (!empty($authError)): ?>
            <p class="form-error"><?= htmlspecialchars((string) $authError, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <div class="auth-tabs">
            <button type="button" class="auth-tab is-active" data-auth-tab="login">Войти</button>
            <button type="button" class="auth-tab" data-auth-tab="register">Регистрация</button>
        </div>
        <form class="auth-form" method="post">
            <input type="hidden" name="auth_action" value="login">
            <label>Email<input type="email" name="email" required maxlength="120" autocomplete="email"></label>
            <label>Пароль<input type="password" name="password" required minlength="6" maxlength="72" autocomplete="current-password"></label>
            <label class="register-only">Повторите пароль<input type="password" name="password_confirmation" minlength="6" maxlength="72" autocomplete="new-password"></label>
            <button class="button button--primary" type="submit">Продолжить</button>
        </form>
    </div>
</div>
