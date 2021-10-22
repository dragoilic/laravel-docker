<?php
namespace App\Http\Transformers\App;

use App\Betting\SportEvent\League;
use League\Fractal\TransformerAbstract;

class LeagueTransformer extends TransformerAbstract
{
    public function transform(League $league)
    {
        $group = $this->getGroup($league->getName());
        $rank = $this->getRank($group, $league->getSportId());
        return [
            'id' => $league->getId(),
            'name' => $league->getName(),
            'group' => $group,
            'rank' => $rank,
            'provider' => $league->getProvider(),
        ];
    }

    private function getGroup(String $leagueName): String {
        if (str_contains(strtoupper ($leagueName), "NFL")) {
            return "NFL";
        } else if (str_contains(strtoupper ($leagueName), "NCAA")) {
            return "NCAA";
        } else if (str_contains(strtoupper ($leagueName), "AFL")) {
            return "AFL";
        } else if (str_contains(strtoupper ($leagueName), "NBA")) {
            return "NBA";
        } else if (str_contains(strtoupper ($leagueName), "WNBA")) {
            return "WNBA";
        }
        else if (str_contains(strtoupper ($leagueName), "WNCAA")) {
            return "WNCAA";
        } else if (str_contains(strtoupper ($leagueName), "NHL")) {
            return "NHL";
        } else if (str_contains(strtoupper ($leagueName), "MLB")) {
            return "MLB";
        } else if (str_contains(strtoupper ($leagueName), "AAA")) {
            return "AAA";
        }
        return $leagueName;
    }

    private function getRank(String $groupName, String $sportId): String {
        switch($groupName) {
            case "NFL":
                return "101";
                break;
            case "AFL":
                return "105";
                break;
            case "NBA":
                return "201";
                break;
            case "WNBA":
                return "202";
                break;
            case "NHL":
                return "301";
                break;
            case "MLB":
                return "401";
                break;
            case "AAA":
                return "405";
                break;              
            case "NCAA":
                if ($sportId === '131506') {
                    return 103;
                } else if ($sportId === '48242') {
                    return 203;
                } else if ($sportId === '35232') {
                    return 303;
                } else if ($sportId === '154914') {
                    return 403;
                }
                break;
            case "WNCAA":
                if ($sportId === '131506') {
                    return 104;
                } else if ($sportId === '48242') {
                    return 204;
                } else if ($sportId === '35232') {
                    return 304;
                } else if ($sportId === '154914') {
                    return 404;
                }
                break;
            default:
                return "999";
        }

    }
}
