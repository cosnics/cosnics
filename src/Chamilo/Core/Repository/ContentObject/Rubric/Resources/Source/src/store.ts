export default {
    uiState: {
        builder: {
            showSplitView: false,
            selectedCriterium: '',
            selectedClusterView1: '',
            selectedClusterView2: ''
        },
        entry: {
            evaluator: '',
            showDefaultFeedbackFields: false
        }
    },
    rubricData: {
        "id":"t9hnqokwvenfdgskc7ykas",
        "useScores":true,
        "title":"Een rubric",
        "levels": [
            {
                "id":"ll1mno0a18hjedwy6w3jt",
                "title":"Overstijgt de verwachtingen",
                "description":"",
                "score":10,
                "isDefault":false
            },
            {
                "id":"icoz2ha4bahqvqfi71slz",
                "title":"Voldoet aan de verwachtingen",
                "description":"",
                "score":7,
                "isDefault":false
            },
            {
                "id":"ewl5t7raruutqf4gudx15",
                "title":"Voldoet bijna aan de verwachtingen",
                "description":"",
                "score":4,
                "isDefault":false
            },
            {
                "id":"fcd66dsoj78g8jq0diks3b",
                "title":"Voldoet niet aan de verwachtingen",
                "description":"",
                "score":0,
                "isDefault":false
            }
        ],
        "clusters": [
            {
                "id":"fbb3yazs9iaxc8zff4jurm",
                "title":"Een rubric",
                "categories": [
                    {
                        "id":"r7z43w6cuzijn6782siusl",
                        "title":"Professioneel Communiceren",
                        "color":"#00943E",
                        "criteria": [
                            {
                                "id":"fufp9nyhm59tpv834p7s5",
                                "title":"Volledigheid antwoorden",
                                "weight":100
                            },
                            {
                                "id":"yjz8854ozprzhr2hw45wup",
                                "title":"Onderbouwde mening",
                                "weight":100
                            }
                        ]
                    },
                    {
                        "id":"w1qzngnd3yawww7dw4r36",
                        "title":"Categorie 2",
                        "color":"#0182ED",
                        "criteria": [
                            {
                                "id":"bw5jbflbi7nkg78120bu",
                                "title":"Volledigheid antwoorden",
                                "weight":100
                            },
                            {
                                "id":"pqkx9h654qyix2dz78hok",
                                "title":"Project stakeholders defined",
                                "weight":100
                            }
                        ]
                    }
                ],
                "criteria": []
            },
            {
                "id":"v6ptmrbfp829l5wir7y1c",
                "title":"Cluster 1",
                "categories": [
                    {
                        "id":"qkpojkdo7195jmdwgw29sb",
                        "title":"Categorie 3",
                        "color":"#E76F01",
                        "criteria": [
                            {
                                "id":"9ec44j2au3v1nl9sigksrq",
                                "title":"Nog een laatste criterium",
                                "weight":100
                            }
                        ]
                    }
                ],
                "criteria": []
            },
            {
                "id":"iltm24mgoemsoc8okbny",
                "title":"Een tweede cluster",
                "categories": [
                    {
                        "id":"iuxmxxycunsonrldrk16m",
                        "title":"",
                        "color":"",
                        "criteria": []
                    }
                ],
                "criteria": []
            }
        ],
        "choices": [
            {
                "selected":false,
                "feedback":"Student geeft steeds volledige en betrouwbare informatie. Alle informatie is opgenomen in de antwoorden.",
                "hasFixedScore":false,
                "fixedScore":10,
                "criteriumId":"fufp9nyhm59tpv834p7s5",
                "levelId":"ll1mno0a18hjedwy6w3jt"
            },
            {
                "selected":false,
                "feedback":"Student geeft soms volledige en betrouwbare informatie. Niet alle informatie is opgenomen in de antwoorden.",
                "hasFixedScore":false,
                "fixedScore":10,
                "criteriumId":"fufp9nyhm59tpv834p7s5",
                "levelId":"icoz2ha4bahqvqfi71slz"
            },
            {
                "selected":false,
                "feedback":"Student geeft zo goed als altijd onvolledige en twijfelachtige informatie die vragen oproept.",
                "hasFixedScore":false,
                "fixedScore":10,
                "criteriumId":"fufp9nyhm59tpv834p7s5",
                "levelId":"ewl5t7raruutqf4gudx15"
            },
            {
                "selected":false,
                "feedback":"Student geeft zijn mening onderbouwd en overtuigend.",
                "hasFixedScore":false,
                "fixedScore":10,
                "criteriumId":"fufp9nyhm59tpv834p7s5",
                "levelId":"fcd66dsoj78g8jq0diks3b"
            },
            {
                "selected":false,
                "feedback":"Student geeft steeds volledige en betrouwbare informatie. Alle informatie is opgenomen in de antwoorden.",
                "hasFixedScore":false,
                "fixedScore":10,
                "criteriumId":"yjz8854ozprzhr2hw45wup",
                "levelId":"ll1mno0a18hjedwy6w3jt"
            },
            {
                "selected":false,
                "feedback":"Student geeft steeds volledige en betrouwbare informatie. Alle informatie is opgenomen in de antwoorden.",
                "hasFixedScore":false,
                "fixedScore":10,
                "criteriumId":"yjz8854ozprzhr2hw45wup",
                "levelId":"icoz2ha4bahqvqfi71slz"
            }
        ]
    },
    rubricResults: {
        "evaluators": ["student", "docent", "coach"],
        "evaluations": {
            "docent": [
                {"criteriumId":"fufp9nyhm59tpv834p7s5","levelId":"ll1mno0a18hjedwy6w3jt","feedback":"Uitstekend!"},
                {"criteriumId":"yjz8854ozprzhr2hw45wup","levelId":"ll1mno0a18hjedwy6w3jt","feedback":"Flink gedaan!"},
                {"criteriumId":"bw5jbflbi7nkg78120bu","levelId":"ll1mno0a18hjedwy6w3jt","feedback":""},
                {"criteriumId":"pqkx9h654qyix2dz78hok","levelId":"ewl5t7raruutqf4gudx15","feedback":"Hier had ik toch meer van verwacht"},
                {"criteriumId":"9ec44j2au3v1nl9sigksrq","levelId":"icoz2ha4bahqvqfi71slz","feedback":""}
            ],
            "student": [
                {"criteriumId":"fufp9nyhm59tpv834p7s5","levelId":"ll1mno0a18hjedwy6w3jt","feedback":"Ik vond het best moeilijk maar uiteindelijk lukte het wel."},
                {"criteriumId":"yjz8854ozprzhr2hw45wup","levelId":"ll1mno0a18hjedwy6w3jt","feedback":""},
                {"criteriumId":"bw5jbflbi7nkg78120bu","levelId":"icoz2ha4bahqvqfi71slz","feedback":""},
                {"criteriumId":"pqkx9h654qyix2dz78hok","levelId":"icoz2ha4bahqvqfi71slz","feedback":""},
                {"criteriumId":"9ec44j2au3v1nl9sigksrq","levelId":"icoz2ha4bahqvqfi71slz","feedback":""}
            ],
            "coach": []
        }
    }
};