1. CPP Important Information about the Content of this Folder

All the files inside this folder are only executed when the module CPP is installed. Those files are an important part
of the system since they are used as a complement to train your data. It is not recommended to remove them since they
may be useful again in case the module is uninstalled and installed again.

If you need to move those files to a different folder, then you'll need to modify the path of those files in .install
file of this module. In addition, it is not recommended to use 'file_get_contents' function to get the contents of those
files if you move them to another place outside the root module since that function will load all their content in
memory, and since they are big files, you may experience some slowness in your system. So, please in case it is not
strictly necessary don't move those files from their original folder.