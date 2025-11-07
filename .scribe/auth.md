# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer {YOUR_ACCESS_TOKEN}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

You can obtain your access token by logging in through the `/api/authentication/login` endpoint. Include the token in the Authorization header as `Bearer {YOUR_ACCESS_TOKEN}`.
