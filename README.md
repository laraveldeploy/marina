# Server Ninja

- In the filament admin panel, there is a Server resource
  - It has a manage page. Inside, we want to run a long running process and see the output in real time (or similar).
  - There is a Commands\LongOutputCommand.php file that does this just for POC.
- There is already the provision Docker action Actions\ServerInstallDocker
- There's the ProcessController that is used to try the Buffer Streaming with PHP and SSE.