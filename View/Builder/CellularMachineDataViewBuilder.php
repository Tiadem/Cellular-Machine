<?php

namespace View\Builder;

use CellularMachine\CellularMachinePatternTransformer;
use View\Strategy\CreateStepViewStrategyInterface;

class CellularMachineDataViewBuilder{

    private string $activeView = 'sumRowValuesWithLog';
    private string $randomViewID;

    public function __construct(
        private CellularMachinePatternTransformer $transformer,
        private CreateStepViewStrategyInterface $strategy
    ){
        $this->randomViewID = uniqid();
    }

    public function setStrategy(CreateStepViewStrategyInterface $strategy): self{
        $this->strategy = $strategy;
        return $this;
    }
    public function changeTransformer(CellularMachinePatternTransformer $transformer): self{
        $this->transformer = $transformer;
        return $this;
    }

    public function setActiveView(string $activeStep): self{
        $this->activeView = $activeStep;
        return $this;
    }

    public function generateRandomViewID(): self{
        $this->randomViewID = uniqid();
        return $this;
    }

    public function createNavigation(array $navigationParameters): string{

        $navigationElementsArray = array_map(
            fn(array $navigationElementParameters) =>
            $this->createNavigationElement(
                $navigationElementParameters['transformStep'],
                $navigationElementParameters['title']
            ),
            $navigationParameters
        );
        $navigationBody = implode(' ',$navigationElementsArray);

        return sprintf('
          <ul class="nav nav-pills" role="tablist">
            %s
          </ul>
        ',$navigationBody);
    }

    public function createNavigationElement(string $stepName,string $title): string{

        return  sprintf('    
            <li class="nav-item" role="presentation">
                <button
                        class="nav-link %3$s"
                        data-bs-toggle="tab"
                        data-bs-target="#%1$s"
                        type="button"
                        role="tab"
                        aria-controls="%1$s"
                        aria-selected="false"
                >
                    %2$s
                </button>
            </li>
            ',
            $this->getRandomStepName($stepName),
            $title,
            $this->isActiveView($stepName) ? 'active' : ''
        );
    }

    public function createBody(array $stepViewParameters): string{

        $stepViewsArray = array_map(
            fn(array $parameters) =>
                $this->setStrategy($parameters['strategy'])
                    ->createStepView($parameters['transformStep'])
            ,
            $stepViewParameters
        );

        $body = implode(' ',$stepViewsArray);

        return sprintf('
          <div class="tab-content">
            %s
          </div>
        ',$body);
    }

    public function createStepView(string $stepName): string{
        return sprintf('
            <div class="tab-pane fade %1$s" id="%2$s" role="tabpanel" aria-labelledby="%2$s">
                <table>
                    %3$s
                    %4$s 
                </table>
            </div>',
            $this->isActiveView($stepName) ? implode(' ',['show','active']) : '',
            $this->getRandomStepName($stepName),
            $this->createStepHeader(),
            $this->strategy->createStepViewBody($this->transformer->$stepName())
        );
    }

    private function createStepHeader(): string{
        return '
        <tr>
            <th>Index</th>
            <th>Wartości</th>
        </tr>';
    }

    private function getRandomStepName(string $stepName): string{
        return "$stepName-$this->randomViewID";
    }

    private function isActiveView($currentStep): bool{
        return $this->activeView === $currentStep;
    }

}
