# @todo Version number releases
module.exports =
  dist:
    options:
      mode: 'zip'
      archive: 'releases/wp-better-attachments.zip'
    expand: true,
    cwd: 'releases/',
    src: ['wp-better-attachments/**/*'],