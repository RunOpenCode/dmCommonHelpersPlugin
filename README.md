dmCommonHelpersPlugin for Diem Extended
===============================

Author: [TheCelavi](http://www.runopencode.com/about/thecelavi), [Grouchy](http://www.runopencode.com/about/grouchy)
Version: 0.0.1
Stability: Stable  
Date: December 9th, 2012  
Courtesy of [Run Open Code](http://www.runopencode.com)   
License: [Free for all](http://www.runopencode.com/terms-and-conditions/free-for-all)

dmCommonHelpersPlugin for Diem Extended is collection of common template helpers
that are used in various plugins provided by RunOpenCode, and, of course, they
can be used for projects as well.

Additional helpers are most welcome.

Current implemented helpers
---------------------

###`File` helper functions:

- `format_file_size_from_bytes`: Formats size of file in bytes to bytes, KBs, MBs or GBs with size label
- `format_posix_file_permissions_to_human`: Formats posix file permissions to human 
- `get_posix_file_owner_info_by_id`: Returns required info regarding owner
- `get_posix_file_group_info_by_id`: Returns required info regarding group
- `get_file_properties`: Returns common OS properties for file HDD
 
### `Word` helper functions:

- `get_reduced_raw_text`: Reduce given raw text to defined number of words or chars