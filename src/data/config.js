import { reactive } from "vue";

export const config = reactive({
  version: "5.0",
  author: "Dan Leach",
  web: "https://danleach.uk",
  repo: "https://github.com/dan-leach/learnloop",
  email: "mail@learnloop.co.uk",
  feedback: {
    create: {
      questions: {
        types: {
          text: {
            name: "Text",
            id: "text",
            settings: {
              required: true,
              optionsLimit: 0,
              characterLimit: {
                min: 1,
                max: 500,
              },
            },
          },
          select: {
            name: "Drop-down select",
            id: "select",
            settings: {
              required: true,
              optionsLimit: 20,
            },
          },
          checkbox: {
            name: "Checkboxes",
            id: "checkbox",
            settings: {
              required: true,
              optionsLimit: 20,
              selectedLimit: {
                min: 1, //default - can be changed by facilitator
                max: 1, //will automatically increase to the number of options unless changed
              },
            },
          },
        },
        minimumOptions: 2,
      },
    },
  },
  interaction: {
    join: {
      currentIndexPollInterval: 3000,
    },
    host: {
      newSubmissionsPollInterval: 3000,
    },
    create: {
      slides: {
        types: {
          //interactive slides
          singleChoice: {
            name: "Single choice",
            id: "singleChoice",
            isInteractive: true,
            settings: {
              charts: ["bar", "doughnut"],
              optionsLimit: 10, //fixed
              submissionLimit: 1, //default - can be changed by facilitator
              hideResponses: false,
            },
          },
          multipleChoice: {
            name: "Multiple choice",
            id: "multipleChoice",
            isInteractive: true,
            settings: {
              charts: ["bar", "doughnut"],
              optionsLimit: 10,
              selectedLimit: {
                min: 1, //default - can be changed by facilitator
                max: 1, //will automatically increase to the number of options unless changed
              },
              submissionLimit: 1,
              hideResponses: false,
            },
          },
          freeText: {
            name: "Free text",
            id: "freeText",
            isInteractive: true,
            settings: {
              optionsLimit: 0,
              characterLimit: {
                min: 1,
                max: 200,
              },
              submissionLimit: 10,
              hideResponses: false,
            },
          },
          //static slides
          static: {
            name: "Static",
            id: "static",
            isInteractive: false,
          },
        },
        submissionLimitMax: 100,
        minimumOptions: 2,
        images: {
          max: 10,
          maxSize: 500000,
        },
      },
    },
  },
  client: {
    url: "https://dev.learnloop.co.uk",
    isFocusView: false,
    showApiConsole: true,
    subsessionEmailPrompt: true,
    regions: [
      //each array is of the hospitals in that region
      {
        name: "Northern Ireland",
        organisations: [
          "Altnagelvin Area Hospital, Londonderry",
          "Antrim Area Hospital, Antrim",
          "Causeway Hospital, Coleraine",
          "Craigavon Area Hospital, Craigavon",
          "Daisy Hill Hospital, Newry",
          "Royal Belfast Hospital for Sick Children, Belfast",
          "South West Acute Hospital, Enniskillen",
          "Ulster Hospital, Dundonald",
        ],
      },
      {
        name: "Scotland",
        organisations: [
          "Lothian",
          "Greater Glasgow",
          "Wishaw",
          "Borders",
          "Ayrshire & Arran",
          "Dumfries",
          "Highlands",
          "Grampian",
          "Tayside",
          "Fife",
          "Forth Valley",
        ],
      },
      {
        name: "Wales",
        organisations: [
          "Bronglais General Hospital",
          "Glan Clwyd Hospital",
          "Glangwili General Hospital",
          "Grange University Hospital",
          "Morriston Hospital",
          "Neath Port Talbot Hospital",
          "Prince Charles Hospital",
          "Princess of Wales Hospital",
          "Royal Glamorgan Hospital",
          "University Hospital of Wales",
          "Withybush General Hospital",
          "Wrexham Maelor Hospital",
          "Ysbyty Gwynedd",
        ],
      },
      {
        name: "East Midlands",
        organisations: [
          "Boston Pilgrim Hospital",
          "Chesterfield Royal",
          "Derby Hospitals",
          "Grantham and District Hospital",
          "Kettering General Hospital",
          "Leicester Royal Infirmary",
          "Lincoln County Hospital",
          "Northampton General Hospital",
          "Nottingham University Hospitals",
          "Sherwood Forest Hospitals",
        ],
      },
      {
        name: "East of England",
        organisations: [
          "Addenbrookes Hospital",
          "Basildon and Thurrock University Hospital",
          "Bedford Hospital",
          "Broomfield Hospital",
          "Colchester Hospital",
          "East & North Herts",
          "Hinchingbrooke Health Care",
          "Ipswich Hospital",
          "James Paget University Hospital",
          "Luton & Dunstable University Hospital",
          "Norfolk & Norwich University Hospital",
          "Peterborough City Hospital",
          "Princess Alexandra Hospital",
          "Queen Elizabeth Hospital Kings Lynn",
          "Queen Elizabeth II Hospital",
          "Southend University Hospital",
          "Watford General Hospital",
          "West Suffolk Hospital",
        ],
      },
      {
        name: "North East and North Cumbria",
        organisations: [
          "Bishop Aukland Hospital",
          "Darlington Memorial Hospital",
          "Friarage Hospital",
          "Great North Children's Hospital",
          "North Tyneside General Hospital",
          "Queen Elizabeth Hospital",
          "South Tyneside District Hospital",
          "Sunderland Royal Hospital",
          "The Cumberland Infirmary",
          "The James Cook University Hospital",
          "University Hospital of Hartlepool",
          "University Hospital of North Durham",
          "University Hospital of North Tees",
          "West Cumberland Hospital",
        ],
      },
      {
        name: "North West",
        organisations: [
          "Alder Hey Children’s",
          "Blackpool Teaching Hospitals",
          "Bolton",
          "Central Manchester University Hospitals",
          "Countess of Chester Hospital",
          "East Cheshire",
          "East Lancashire Hospitals",
          "Lancashire Teaching Hospitals",
          "Mid Cheshire Hospitals",
          "Salford Royal",
          "Southport and Ormskirk Hospital",
          "St Helens and Knowsley Teaching Hospitals",
          "Stockport",
          "Tameside & Glossop Integrated Care",
          "The Pennine Acute Hospitals",
          "University Hospital of South Manchester",
          "University Hospitals of Morecambe Bay",
          "Warrington and Halton Hospitals",
          "Wirral University Teaching Hospital",
          "Wrightington Wigan and Leigh",
        ],
      },
      {
        name: "South East Coast and London Partnership",
        organisations: [
          "Barnet General Hospital",
          "Buckland Hospital",
          "Central Middlesex",
          "Chelsea and Westminster",
          "Conquest Hospital",
          "Croydon University Hospital",
          "Darent Valley Hospital",
          "Ealing Hospital",
          "East Surrey Hospital",
          "Eastbourne District General Hospital",
          "Epsom Hospital",
          "Evelina London Children’s Hospital",
          "Frimley Park Hospital",
          "Great Ormond Street Hospital for Children",
          "Hillingdon Hospitals",
          "Kent and Canterbury Hospital",
          "King George Hospital",
          "King's College Hospital",
          "Kingston Hospital",
          "Lewisham Hospital",
          "Maidstone Hospital",
          "Medway Maritime Hospital",
          "Newham University Hospital",
          "North Middlesex University Hospital",
          "Northwick Park",
          "Princess Royal University Hospital",
          "Queen Elizabeth Hospital",
          "Queen Elizabeth The Queen Mother Hospital",
          "Queen Mary's Hospital",
          "Queen's Hospital",
          "Royal Alexandra Children's Hospital",
          "Royal Free London",
          "Royal Surrey County Hospital",
          "Royal Victoria Hospital",
          "St George’s Hospital",
          "St Helier Hospital",
          "St. Mary’s Hospital",
          "St. Peter's Hospital",
          "St. Richards Hospital",
          "The Royal London Hospital",
          "The Whittington Hospital",
          "Tunbridge Wells Hospital",
          "University College Hospital",
          "West Middlesex University Hospital",
          "Whipps Cross University Hospital",
          "William Harvey Hospital",
          "Worthing Hospital",
        ],
      },
      {
        name: "South West",
        organisations: [
          "Gloucestershire Hospitals",
          "Great Western Hospitals",
          "North Bristol",
          "Northern Devon Hospital",
          "Plymouth Hospitals",
          "Royal Cornwall Hospitals",
          "Royal Devon and Exeter Hospitals",
          "Royal United Hospitals Bath",
          "South Devon Healthcare",
          "Taunton and Somerset",
          "University Hospitals Bristol",
          "Yeovil District Hospital",
        ],
      },
      {
        name: "Thames Valley",
        organisations: [
          "Frimley Health",
          "John Radcliffe Hospital",
          "Milton Keynes Hospital",
          "Royal Berkshire",
          "Stoke Mandeville Hospital",
          "Wycombe Hospital",
        ],
      },
      {
        name: "Wessex",
        organisations: [
          "Basingstoke and North Hampshire Hospital",
          "Dorset County Hospital",
          "Poole Hospital",
          "Queen Alexandra Hospital",
          "Royal Hampshire County Hospital",
          "Salisbury District Hospital",
          "Southampton General Hospital",
          "St. Mary's Hospital",
        ],
      },
      {
        name: "West Midlands",
        organisations: [
          "Alexandra Hospital",
          "Birmingham Children’s Hospital",
          "Birmingham City Hospital",
          "Burton Hospitals",
          "County Hospital (Stafford)",
          "County Hospital (Wye Valley)",
          "George Eliot Hospital",
          "Good Hope Hospital",
          "Heartlands Hospital",
          "Hospital of St Cross",
          "Kidderminster Hospital",
          "New Cross Hospital",
          "Princess Royal Hospital",
          "Royal Shrewsbury Hospital",
          "Royal Stoke University Hospital",
          "Russells Hall Hospital",
          "Sandwell General Hospital",
          "Solihull Hospital",
          "South Warwickshire",
          "University Hospital Coventry",
          "Walsall Manor Hospital",
          "Worcestershire Royal Hospital",
        ],
      },
      {
        name: "Yorkshire and Humber",
        organisations: [
          "Airedale General Hospital",
          "Barnsley Hospital",
          "Bassetlaw Hospital",
          "Bradford Royal Infirmary",
          "Calderdale Royal Hospital",
          "Dewsbury and District Hospital",
          "Diana, Princess of Wales Hospital",
          "Doncaster Royal Infirmary",
          "Harrogate District Hospital",
          "Huddersfield Royal Infirmary",
          "Hull Royal Infirmary",
          "Leeds Children's Hospital",
          "Pinderfields General Hospital",
          "Pontefract General Infirmary",
          "Rotherham Hospital",
          "Scarborough Hospital",
          "Scunthorpe General Hospital",
          "Sheffield Children's Hospital",
          "St.Luke's Hospital",
          "The York Hospital",
        ],
      },
    ],
  },
  api: {
    url: "https://dev.learnloop.co.uk/api/",
    imagesUrl: "https://dev.learnloop.co.uk/api/interaction/uploads/img/",
    timeoutDuration: 15000,
  },
});
