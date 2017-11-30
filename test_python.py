host = "\ufeff192.1.100.14"
#host = "192.1.100.13"
host = host.replace("\ufeff", "")

print host

# bugged bot when call only "@bot_name 'server' <server_name>"
# check lastchange status and save to database if status is difference 
# write server get request and echo back to client with all list of server (done)
# write about user permission for line chat bot