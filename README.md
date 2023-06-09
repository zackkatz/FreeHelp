# FreeHelp

FreeHelp makes FreeScout look and feel more like Help Scout, adding many small quality-of-life improvements.

- Don't open anything in new tabs/windows! [#2914](https://github.com/freescout-helpdesk/freescout/issues/2914)
- Shift-click checkboxes to select a range of conversations at once [#1312](https://github.com/freescout-helpdesk/freescout/issues/1312)
- Convert Dropdown-type Custom Fields to Select2 inputs
- Type `/` to quickly access search ([a feature request](https://feedback.userreport.com/25a3cb5f-e4bd-4470-b6f3-79fcfaa8e90f/#idea/393550))
- Type `Escape` to close dropdowns, cancel editing conversation titles, and cancel editing a note
- Apply many CSS tweaks to make FreeScout look and feel more like Help Scout
  - Wrap Custom Fields in the familiar gray box
  - Remove many of the blue highlights in the FreeScout interface
  - Match more line spacing and font styles/sizings
  - Set the sidebar background color to gray and sidebar panel colors to white
  - Highlight selected rows in the conversation table
  - Hide the text color formatting button from the reply editor
  - Make the modal look more like the Help Scout modal
  - And much more!

## Known bugs

- [ ] The modal is too low when using editing a customer in the CRM module
- [ ] The brand icon is too large when on the main FreeScout mailboxes screen

## Wishlist

- [ ] Improve the icons bundled with FreeScout
- [ ] Implement a nicer search modal
- [ ] Convert CSS and JS to external files

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

## Inspiration

* This project was inspired by [DropkickJS](https://github.com/fulldecent/freescout-dropkick-js).
