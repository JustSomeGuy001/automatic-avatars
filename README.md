# Automatic Avatars
Autoavatars plugin for Oxwall. Assigns default avatars to new users.

It seems like people have been asking for this for years, so I decided it was about time someone finally built a free, open-source plugin for assigning avatars to new users based on their gender.

![Screenshot from 2020-07-02 09-38-32](https://user-images.githubusercontent.com/25450448/86574400-c9592c80-bf43-11ea-9637-695852543539.png)

## Feature requests 

I'm not currently planning on adding any major features. However, if I get many requests, I may consider revamping the plugin to include options for assigning avatars by: 

- Account Type
- User Role
- The profile question of your choice

## Notes on plug-in operation

This plugin does NOT change the default Oxwall avatar, as that would require changes to the Oxwall core, which is not allowed by the Oxwall plugin development guidelines. Instead, it operates by assigning an avatar to newly registered users. 

- Avatars will be assigned to new users only. Existing users will retain their standard Oxwall default avatar.

- Users can delete the avatars that you assign with this plugin. If they do so, their avatar will revert back to the standard Oxwall default avatar.

- Avatars with transparent backgrounds are recognized and allowed by this plugin, but they are not recommended. As of version 1.8.x, Oxwall converts all uploaded avatars to .jpg images, which will cause unexpected results in images with transparent backgrounds. 

Please report any bugs, and feel free to offer suggestions or feature-requests.
