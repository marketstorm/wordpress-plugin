# MarketStorm Wordpress Plugin

Wordpress plugin for Marketstorm Pixels.

## Features

- Multisite and Single site Wordpress instances
- Compatible with Matomo Tag Manager Pixels
- Ensures that pixel execution is not deferred by Nitropack, Jetpack, or Cloudflare

## Installation and Usage

1) **Download** the latest release from [here](https://github.com/marketstorm-ai/wordpress-plugin/releases/latest)
   - Choose the *Source code (zip)* file under the Assets tab

3) **Upload Plugin** to Wordpress
   **- Plugins -> Add New -> Upload Plugin**


![Wordpress Plugin Upload](/docs/wordpress_upload.png)


**WARNING: Network Activate** will activate the plugin for all sites on a **Wordpress Multisite Instance**. Unless that is desired, the relevant sites should be **activated manually**.

**3) Activate the plugin for the given website**
**4) Find the Container ID in Matomo**

![Matomo Container ID](/docs/container_id.png)

5) On **Wordpress** in **Settings -> Marketstorm**, enter the Matomo **Container ID** for the Tag Manager and **Save**

![Wordpress Settings](/docs/wordpress_settings.png)

6) If needed, a plugin update check can be triggered using the **Update** link on the MarketStorm entry of the **Plugin List**.
   - Next to the plugin's Activate/Deactivate buttons

## Important Notes
- The Container ID stored by the plugin will not persist upon deactivation
- If the Container ID is left blank, no code will be injected.
