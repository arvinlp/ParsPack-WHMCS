<div class="container">
    <h2>مدیریت سرور مجازی</h2>

    {if $error}
        <div class="alert alert-danger">
            {$error}
        </div>
    {else}
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        وضعیت سرور
                    </div>
                    <div class="card-body">
                        <p><strong>وضعیت:</strong> {$status}</p>
                        <p><strong>آی‌پی:</strong> {$server.ip}</p>
                        <p><strong>CPU Usage:</strong> {$server.cpu_usage}%</p>
                        <p><strong>RAM Usage:</strong> {$server.ram_usage}%</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        عملیات
                    </div>
                    <div class="card-body">
                        <form method="post" action="clientarea.php?action=productdetails&id={$serviceid}">
                            <input type="hidden" name="token" value="{$csrf_token}">
                            <input type="hidden" name="moduleaction" value="reboot">
                            <button type="submit" class="btn btn-primary">راه‌اندازی مجدد</button>
                        </form>
                        <form method="post" action="clientarea.php?action=productdetails&id={$serviceid}">
                            <input type="hidden" name="token" value="{$csrf_token}">
                            <input type="hidden" name="moduleaction" value="shutdown">
                            <button type="submit" class="btn btn-warning">خاموش کردن</button>
                        </form>
                        <form method="post" action="clientarea.php?action=productdetails&id={$serviceid}">
                            <input type="hidden" name="token" value="{$csrf_token}">
                            <input type="hidden" name="moduleaction" value="poweron">
                            <button type="submit" class="btn btn-success">روشن کردن</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    {/if}
</div>