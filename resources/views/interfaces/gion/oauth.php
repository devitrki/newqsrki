<!DOCTYPE html>
<html>

<head>
    <title>Contoh HTML FORM POST</title>
</head>

<body>
    <form action="https://accurate.id/oauth/authorize" method="post">
        <input name="client_id" value="42f12a10-08df-4b91-b1e4-c4465d686072" />
        <input name="response_type" value="token" />
        <input name="redirect_uri" value="https://example.com/aol-oauth-callback" />
        <input name="scope" value="item_view item_save sales_invoice_view" />
        <button type="submit">Submit</button>
    </form>
</body>