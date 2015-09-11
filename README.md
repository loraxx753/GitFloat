# GitFloat

GitFloat is a git reporter for GitHub that allows you to see various reports about your organization's github repositories through user defined widgets in a GUI like environment.

## Getting Started

GitFloat is easy to install, with very few real requirements.

### Requirements

* PHP >=5.4
* For development environments, [Vagrant](https://www.vagrantup.com/) is reccommended.

### Setting Up

To set up GitFloat, copy the `config_template.json` files and name the new files `config.json`

#### The Main Config File

You would end up with a file at `/config/config.json` with the following:

```json
{
	"widgets" : {
		"PAGENAME" : [
			"AUTHOR/WIDGET_NAME",
			"AUTHOR/WIDGET_NAME",
			"AUTHOR/WIDGET_NAME"
		]

	},
	"homepage" : "HOMEPAGE_NAME",
	"timezone" : "America/Chicago"
}
```

There are a few components to this:

* `"widgets"` is your main list of widgets, broken up by `"PAGENAME"`.
 * Each `"PAGENAME"` represents a list of widgets you would like on that page (so for example `"home" {}, "stats" {}` )
  * `"AUTHOR/WIDGET_NAME"` maps to the widgets found in your `/app/import` folder.
* `"homepage"` is the page you would like mapped to `/`. It will not show on the nav bar and will instead be navigated to by clicking the "GitFloat" banner.
* `"timezone"` is what timezone you would like GitFloat to show, please see PHP's [list of timezones](http://php.net/manual/en/timezones.php) for what to put here.

#### The dev/prod Config File

The '/config/dev' and '/config/prod' config file function exactly the same. The one chosen is based on the `APP_ENV` environment variable set on your server. For help with setting the environment variable, plese see [this helpful stackexchange question](http://unix.stackexchange.com/questions/117467/how-to-permanently-set-environmental-variables).

The default folder it will look in is '/config/dev' if no `APP_ENV` is set on the server. Honestly, you can really make any environment you want to as long as there's a folder for it. For example, if I set the `APP_ENV` variable to 'foo', then GitFloat will look in the `/config/foo` folder for the `config.json` file.

For this README, we'll just use the dev environment.

##### Creating the dev Config File

Go into `/config/dev/`, make a copy of `config_template.json`, and call that copy `config.json`.

Opening it up, it looks like the following:

```json
{
	"github" : {
		"client_id" :  "GITHUB_APP_CLIENT_ID",
		"client_secret" :  "GITHUB_APP_CLIENT_SECRET"
	}
}
```

You need to set up an [application account](https://github.com/settings/applications/new) (the callback url would be http://yourdomain.com/login.php), then copy/paste the client id and client secret in the `config.json` file and you'll be able to have users sign in to see their repositories.

## Finishing Up

Once everything is set, simply run `./build` from the root directory and you'll be good to go!
