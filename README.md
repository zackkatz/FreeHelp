# FreeHelp

FreeHelp makes FreeScout look and feel more like Help Scout, adding many small quality-of-life improvements.

- Don't open anything in new tabs/windows! [#2914](https://github.com/freescout-helpdesk/freescout/issues/2914)
- Shift-click checkboxes to select a range of conversations at once [#1312](https://github.com/freescout-helpdesk/freescout/issues/1312)
- Convert Dropdown-type Custom Fields to Select2 inputs
- Type `Escape` to close dropdowns, cancel editing conversation titles, and cancel editing a note
- Use Command + Return to submit a reply or note (or Control + Return in Windows)
- Use nicer [Heroicons](https://heroicons.com) instead of the default FreeScout icons
- Apply many CSS tweaks to make FreeScout look and feel more like Help Scout
  - Wrap Custom Fields in the familiar gray box
  - Replace folder icons with nicer [Heroicons](https://heroicons.com)
  - Remove many of the blue highlights in the FreeScout interface
  - Match more line spacing and font styles/sizings
  - Set the sidebar background color to gray and sidebar panel colors to white
  - Highlight selected rows in the conversation table
  - Hide the text color formatting button from the reply editor
  - Make the modal look more like the Help Scout modal
  - And much more!

## Known bugs

- [ ] The modal is too low when using editing a customer in the CRM module

## Todo list

- [x] Improve the icons bundled with FreeScout
- [ ] Convert CSS and JS to external files
- [ ] Add "Customers" as a top-level menu item

## Wish list

- [ ] Implement a nicer search modal

--------------------

## Changelog

### 1.0.3 on August 4, 2023

- Type Command + Return to submit a reply or note (or Control + Return in Windows)
- Replace "Blacklist" with "Blocklist" and "Whitelist" with "Allowlist"

### 1.0.2 on August 4, 2023

- Clean up the sidebar gear icon dropdown menu by removing icons, matching Help Scout
- Add way more Heroicons (https://github.com/zackkatz/FreeHelp/issues/2)[#2]
- Don't hide Remove Formatting, color, underline buttons from editor. Instead, if wanted, add this to the Customization module instead (https://github.com/zackkatz/FreeHelp/issues/4)[#4]
- Improve positioning of the icons in the sidebar buttons

### 1.0.1 on July 21, 2023

- Update icons to use [Heroicons](https://heroicons.com)
- Update user response thread styling to match Help Scout
- Add module image

### 1.0.0 on June 9, 2023

- Initial release

--------------------

## Installation

These instructions assume you installed FreeScout using the [recommended process](https://github.com/freescout-helpdesk/freescout/wiki/Installation-Guide), the "one-click install" or the "interactive installation bash-script", and you are viewing this page using a macOS or Ubuntu system.

Other installations are possible, but not supported here.

1. Download the [latest release of FreeHelp](https://github.com/zackkatz/FreeHelp/releases).

2. Unzip the file locally.

3. Copy the folder into your server using SFTP.

   ```sh
   scp -r ~/Desktop/freehelp-root@freescout.example.com:/var/www/html/Modules/FreeHelp/
   ```

4. SSH into the server and update permissions on that folder.

   ```sh
   chown -R www-data:www-data /var/www/html/Modules/FreeHelp/
   ```

5. Access your admin modules page like https://freescout.example.com/modules/list.

6. Find **FreeHelp** and click ACTIVATE.
