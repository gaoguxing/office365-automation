# Office365 Automation API

### Installation

At first, You should create a new Microsoft App.

[Register an application with microsoft identity platform](https://docs.microsoft.com/en-us/graph/auth-register-app-v2)

The following permissions are required.

> Azure Active Directory Graph

```
Directory.AccessAsUser.All
Directory.Read.All
Directory.Read.All
Directory.ReadWrite.Allm
Directory.ReadWrite.All
User.Read.All
```

> Microsoft Graph

```
Directory.AccessAsUser.All
Directory.Read.All
Directory.ReadWrite.All
User.ReadWrite.All
```

next you should click `Grant consent`

Copy `.env.production` to `.env` and configure it.

### API

#### Create User

> POST http://domain/api/v1/users

##### Body

nickname: Nickname

mail_nickname: Email Prefix

password: Login Password



