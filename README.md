matt/wp-installer
=================

WP Installer CLI Command



Quick links: [Using](#using) | [Installing](#installing) | [Contributing](#contributing)

## Using

This package implements the following commands:

### wp installer install

Install WordPress Core

~~~
wp installer install <dest> [--base_path=<path>] [--base_url=<url>] [--multisite] [--dbuser=<user>] [--dbpass=<pass>] [--dbhost=<host>] [--admin_user] [--admin_password] [--admin_email]
~~~

**OPTIONS**

	<dest>
		The destination for the new WordPress install.

	[--base_path=<path>]
		Optional path to the installation.

	[--base_url=<url>]
		Base URL that sites will be subdirectories of

	[--multisite]
		Convert the install to a Multisite installation.

	[--dbuser=<user>]
		Database username

	[--dbpass=<pass>]
		Database password

	[--dbhost=<host>]
		Database host

	[--admin_user]
		Admin username

	[--admin_password]
		Admin password

	[--admin_email]
		Admin email



### wp installer uninstall

Uninstall the given WordPress install.

~~~
wp installer uninstall <dest> [--base_path=<path>]
~~~

**OPTIONS**

	<dest>
		The site that should be uninstalled.

	[--base_path=<path>]
		Base path that all sites are installed in


## Installing

Installing this package requires WP-CLI v0.23.0 or greater. Update to the latest stable release with `wp cli update`.

Once you've done so, you can install this package with `wp package install matt/wp-installer`.

## Contributing

We appreciate you taking the initiative to contribute to this project.

Contributing isn’t limited to just code. We encourage you to contribute in the way that best fits your abilities, by writing tutorials, giving a demo at your local meetup, helping other users with their support questions, or revising our documentation.

### Reporting a bug

Think you’ve found a bug? We’d love for you to help us get it fixed.

Before you create a new issue, you should [search existing issues](https://github.com/matt/wp-installer/issues?q=label%3Abug%20) to see if there’s an existing resolution to it, or if it’s already been fixed in a newer version.

Once you’ve done a bit of searching and discovered there isn’t an open or fixed issue for your bug, please [create a new issue](https://github.com/matt/wp-installer/issues/new) with the following:

1. What you were doing (e.g. "When I run `wp post list`").
2. What you saw (e.g. "I see a fatal about a class being undefined.").
3. What you expected to see (e.g. "I expected to see the list of posts.")

Include as much detail as you can, and clear steps to reproduce if possible.

### Creating a pull request

Want to contribute a new feature? Please first [open a new issue](https://github.com/matt/wp-installer/issues/new) to discuss whether the feature is a good fit for the project.

Once you've decided to commit the time to seeing your pull request through, please follow our guidelines for creating a pull request to make sure it's a pleasant experience:

1. Create a feature branch for each contribution.
2. Submit your pull request early for feedback.
3. Include functional tests with your changes. [Read the WP-CLI documentation](https://wp-cli.org/docs/pull-requests/#functional-tests) for an introduction.
4. Follow the [WordPress Coding Standards](http://make.wordpress.org/core/handbook/coding-standards/).


*This README.md is generated dynamically from the project's codebase using `wp scaffold package-readme` ([doc](https://github.com/wp-cli/scaffold-package-command#wp-scaffold-package-readme)). To suggest changes, please submit a pull request against the corresponding part of the codebase.*
