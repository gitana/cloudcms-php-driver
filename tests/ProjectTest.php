<?php

namespace CloudCMS\Test;

final class ProjectTest extends AbstractTest
{
    public function testProjects()
    {
        $title = "test " . round(microtime(true) * 1000);
        $projectJob = self::$platform->startCreateProject(array("title"=>$title));
        $jobs = self::$platform->queryJobs(array());
        $this->assertNotEmpty($jobs);

        $projectJob->waitForCompletion();
        $projectId = $projectJob->data["created-project-id"];
        $project = self::$platform->readProject($projectId);
        $this->assertNotNull($project);
        $this->assertEquals($title, $project->data["title"]);

        $projects = self::$platform->listProjects();
        $this->assertNotEmpty($projects);

        $queriedProjects = self::$platform->queryProjects(array());
        $this->assertNotEmpty($queriedProjects);

        $project->delete();
    }
}