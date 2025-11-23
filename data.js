// data.js
// This file contains all channels and the single ClearKey.
// Keep this file private or inside a repository with appropriate access if the streams/keys are sensitive.

window.TV_DATA = {
  // clearKey: "kid_hex:key_hex"  (hex bytes, lowercase/uppercase both acceptable)
  // the single clear key you provided:
  clearKey: "9872e439f21f4a299cab249c6554daa3:0cdfcfe0d0f1fbe100554ce3ef4c4665",

  // channels list. Add one object per channel.
  channels: [
    {
      id: "swarnavahini",
      name: "Swarnavahini",
      logo: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQsbVLTyqSGHTBhoUZ0TZGJ67PpGgZZMPAR7zW3smO9uQ7WUSaxeGcPQ1SNZ-ujvB2a_Zj3AEF4Q3OzhUiqCnRGmgCZUO5PyH4bacqMyQ&s=10",
      mpd: "https://edge1-moblive.yuppcdn.net/drm/smil:swarnawahinidrm.smil/manifest.mpd"
    }

    // Add more channels here. Example:
    // ,{
    //   id: "sirasa",
    //   name: "Sirasa TV",
    //   logo: "https://example.com/sirasa.png",
    //   mpd: "https://example.com/sirasa/manifest.mpd"
    // }
  ]
};
