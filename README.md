# Theme Toolbox 💼 

A Toolset for WP developers to cover common, but not simple features.

```
“A good composer does not imitate; he steals.”
—Igor Stravinsky
```

## What is this?

Through the course of my daily work, I've created solutions to some pretty challenging tasks.  This is where I share them with you, dear developer, so you can learn from them, play with them, and perhaps even use them in your own work. 

***Enjoy!***

## Overview

This plugin isn't meant for your typical WP user, and definitely not in a production environment.  It's built to function as a plugin so you can test it, but it's really a collection of features (that I call "Modules") for you to pull apart and hack.

Each Module directory includes a complete feature that demonstrates how I solved a problem.  Each has its own README.md with detailed information on how it works.

## Features (Modules)

* Responsive Custom HTML5 [Video Player](https://github.com/johnregan3/theme-toolbox/tree/master/modules/video-player)

## Development

The Modules in this plugin provide both Sass and CSS files so that you can modify them based on whatever flavor you prefer.  In order to accomplish this, I write the styles in Sass, then use `gulp` to compile them.

Here are instructions on setting up `gulp` in this Plugin so you can use it as well.

***Installing `gulp` for this Plugin***

- Install Node.js on your computer globally
- Open your terminal and browse to the location of this plugin (`wp-content/plugins/theme-toolbox/`)
- Run: `$ npm install`
- This will download all required tools needed to automate tasks (Be patient, this could take a minute)
- ...
- *Profit*!

***Running Automation***

To work and compile your Sass on the fly, start: `$ gulp watch`.
To exit the `watch` worker, simply type Ctrl+C (Mac).

### Who's to Blame for this?

**John Regan** (johnregan3)
- [johnregan3.com](http://johnregan3.com)
- [Dev Blog](http://johnregan3.co)
- [GitHub](https://github.com/johnregan3)
- [Twitter](https://twitter.com/johnregan3)
- [WordPress](https://profiles.wordpress.org/johnregan3)

*If you have any gripes, complaints or cries of outrage, you can reach me [here](https://www.youtube.com/watch?v=3j9XNhrwVtY).*