<div style="margin-top: 1.5rem; padding: 1rem; background: rgba(20, 184, 166, 0.08); border: 1px solid rgba(15, 118, 110, 0.2); border-radius: 0.75rem; text-align: left;">
    <div style="font-size: 0.75rem; font-weight: 700; color: rgb(15, 118, 110); margin-bottom: 0.75rem;">Akun Demo Admin Panel</div>

    <div style="display: grid; gap: 0.6rem; font-size: 0.75rem; color: #0f172a;">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;">
            <strong style="font-weight: 700;">Username</strong>
            <div style="display: flex; align-items: center; gap: 0.4rem;">
                <span style="background: rgba(255,255,255,0.8); padding: 0.12rem 0.42rem; border-radius: 0.35rem; font-family: monospace;">admin</span>
                <button type="button" style="border: 1px solid rgba(15, 118, 110, 0.25); background: #fff; color: rgb(15, 118, 110); border-radius: 0.35rem; padding: 0.18rem 0.5rem; font-size: 0.65rem; font-weight: 700; cursor: pointer;" onclick="navigator.clipboard.writeText('admin').catch(() => {}); this.textContent='Tersalin'; setTimeout(() => this.textContent='Copy', 1200);">Copy</button>
            </div>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;">
            <strong style="font-weight: 700;">Password</strong>
            <div style="display: flex; align-items: center; gap: 0.4rem;">
                <span style="background: rgba(255,255,255,0.8); padding: 0.12rem 0.42rem; border-radius: 0.35rem; font-family: monospace;">password</span>
                <button type="button" style="border: 1px solid rgba(15, 118, 110, 0.25); background: #fff; color: rgb(15, 118, 110); border-radius: 0.35rem; padding: 0.18rem 0.5rem; font-size: 0.65rem; font-weight: 700; cursor: pointer;" onclick="navigator.clipboard.writeText('password').catch(() => {}); this.textContent='Tersalin'; setTimeout(() => this.textContent='Copy', 1200);">Copy</button>
            </div>
        </div>
    </div>
</div>
