<?php

namespace UNO\EvaluacionesBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CleanDatabaseCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('db:removedups')
            ->setDescription('Remove duplicates')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        ini_set("memory_limit","1000M");
        set_time_limit(600000);

        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $connection = $em->getConnection();

		$output->writeln("Borrando entradas antiguas del log");

        $q = "DELETE FROM Log WHERE Action_idAction = 5";
        $statement = $connection->prepare($q);
        $statement->execute();

        $output->writeln("Obteniendo preguntas duplicadas");

        $qs = "SELECT CONCAT(A.Person_personId,'-',surveyId,'-',questionId) as val, A.answerId
            FROM
                Answer A
            INNER JOIN
	            OptionXQuestion OXQ
                ON A.OptionXQuestion_id = OXQ.OptionXQuestion_id
            INNER JOIN
	        QuestionXSurvey QXS
                ON QXS.QuestionXSurvey_id = OXQ.QuestionXSurvey_id
            INNER JOIN Survey S
	            ON S.surveyId = QXS.Survey_surveyId
            INNER JOIN Question Q
	            ON Q.questionId = QXS.Question_questionId
            GROUP BY questionId, surveyId, Person_personId, AnswerId";

        //$statement = $connection->prepare($q);
        //$statement->execute();
        //$results = $statement->fetchAll();
        $results = $connection->fetchAll($qs);

        $output->writeln("Consulta: ".count($results));

        $filtered = array();

        foreach ($results as $r) {

            $log_info = explode('-',$r['val']);

            if(!array_key_exists($r['val'],$filtered)) {

                $filtered[$r['val']] = array();
            }

            array_push($filtered[$r['val']],array(
                'answer' => $r['answerId'],
                'person' => $log_info[0],
                'survey' => $log_info[1]
            ));
        }

        
        $output->writeln("Eliminando preguntas duplicadas y entradas del log");
        $i = 0;

        foreach ($filtered as $f) {

            if(count($f) > 1){

                foreach($f as $aid){

                    $output->writeln(" ");
                    $output->writeln("Eliminando:\tPregunta: ".$aid['answer']."\tPersona: ".$aid['person']."\tEvaluacion: ".$aid['survey']);

                    $q = "DELETE FROM Answer WHERE Answer.AnswerId = :answer";
                    $statement = $connection->prepare($q);
                    $statement->bindValue('answer',$aid['answer']);
                    $statement->execute();

                    $q = "DELETE FROM Log WHERE (Person_personId = :person AND Survey_surveyId = :survey)";
                    $statement = $connection->prepare($q);
                    $statement->bindValue('person',$aid['person']);
                    $statement->bindValue('survey',$aid['survey']);
                    $statement->execute();

                    $i++;
                }
            }
        }
        $output->writeln("Finalizado de eliminar ".$i." duplicados y limpiar logs");
    }
}